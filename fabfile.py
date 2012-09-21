"""Fabric server config management fabfile.
If you need additional configuration, setup ~/.fabricrc file:

    user = your_remote_server_username

To get specific command help type:
    fab -d command_name

@todo list
    - Add some checkings that remote commands exists: drush, git
    - Handle local installations (update/rebuild) my instance from scratch
    - Add sql dumps from production or devel
"""

import os

from fabric.api import *
from fabric.utils import puts
from fabric import colors
import fabric.network
import fabric.state


JSON_AVAILABLE = True
try:
    import simplejson as json
except ImportError:
    try:
        import json
    except ImportError:
        JSON_AVAILABLE = False

################################
#         ENVIRONMENTS         #
################################

class DictObj(object):
    def __init__(self, d):
        self.d = d

    def __getattr__(self, m):
        return self.d.get(m, None)

def _load_config(**kwargs):
    """Find and parse server config file.

    """
    config, ext = os.path.splitext(kwargs.get('config', 'config.json'))

    if not os.path.exists(config + ext):
        print colors.red('Error. "%s" file not found.' % (config + ext))
        return {}
    elif JSON_AVAILABLE and ext =='.json':
        loader = json
    else:
        print colors.red('Parser package not available')
        return {}
    # Open file and deserialize settings.
    with open(config + ext) as config_file:
        return loader.load(config_file)


def s(*args, **kwargs):
    """Set destination servers or server groups by comma delimited list of names"""
    # Load config
    config = _load_config(**kwargs)
    servers = config['servers']
    # If no arguments were recieved, print a message with a list of available configs.
    if not args:
        print 'No server name given. Available configs:'
        for key in servers:
            print colors.green('\t%s' % key)

    # Create `group` - a dictionary, containing copies of configs for selected servers. Server hosts
    # are used as dictionary keys, which allows us to connect current command destination host with
    # the correct config. This is important, because somewhere along the way fabric messes up the
    # hosts order, so simple list index incrementation won't suffice.
    env.group = {}
    # For each given server name
    for name in args:
        #  Recursive function call to retrieve all server records. If `name` is a group(e.g. `all`)
        # - get it's members, iterate through them and create `group`
        # record. Else, get fields from `name` server record.
        # If requested server is not in the settings dictionary output error message and list all
        # available servers.
        _build_group(name, servers)


    # Copy server hosts from `env.group` keys - this gives us a complete list of unique hosts to
    # operate on. No host is added twice, so we can safely add overlaping groups. Each added host is
    # guaranteed to have a config record in `env.group`.
    env.hosts = env.group.keys()

    # Set local as special server
    setattr(env, 'local', DictObj(config['servers']['local']))

def _build_group(name, servers):
    """Recursively walk through servers dictionary and search for all server records."""
    # We're going to reference server a lot, so we'd better store it.
    server = servers.get(name, None)
    # If `name` exists in servers dictionary we
    if server:
        # check whether it's a group by looking for `members`
        if isinstance(server, list):
            if fabric.state.output['debug']:
                    puts("%s is a group, getting members" % name)
            for item in server:
                # and call this function for each of them.
                _build_group(item, servers)
        # When, finally, we dig through to the standalone server records, we retrieve
        # configs and store them in `env.group`
        else:
            if fabric.state.output['debug']:
                    puts("%s is a server, filling up env.group" % name)
            env.group[server['host']] = server
    else:
        print colors.red('Error. "%s" config not found. Run `fab s` to list all available configs' % name)

def _setup(task):
    """
    Copies server config settings from `env.group` dictionary to env variable.

    This way, tasks have easier access to server-specific variables:
        `env.owner` instead of `env.group[env.host]['owner']`

    """
    def task_with_setup(*args, **kwargs):
        # If `s:server` was run before the current command - then we should copy values to
        # `env`. Otherwise, hosts were passed through command line with `fab -H host1,host2
        # command` and we skip.
        if env.get("group", None):
            for key,val in env.group[env.host].items():
                setattr(env, key, val)
                if fabric.state.output['debug']:
                    puts("[env] %s : %s" % (key, val))

        task(*args, **kwargs)
        # Don't keep host connections open, disconnect from each host after each task.
        # Function will be available in fabric 1.0 release.
        # fabric.network.disconnect_all()
    return task_with_setup

#############################
#          TASKS            #
#############################

@_setup
def update():
    with cd(env.root):
        sudo("git pull origin master")
        sudo("./compress.sh css")
        sudo("./compress.sh js")

@_setup
def clonedb():
    """ Clone SQL database from production to local
    """
    if not hasattr(env, 'local'):
        print colors.red('Usage fab s:prod clonedb')
        return

    sqldump_file_gz = "/tmp/informea_dump.sql.gz"
    sqldump_file = "/tmp/informea_dump.sql"

    print colors.green('Dumping production database to %s' % sqldump_file_gz)
    sudo("mysqldump -u %s --password=%s %s | gzip > %s" % (env.db_user, env.db_password, env.db_database, sqldump_file_gz))

    print colors.green('Downloading production dump to %s' % sqldump_file_gz)
    get(sqldump_file_gz, sqldump_file_gz)
    sudo("rm %s" % sqldump_file_gz)

    print colors.green('Unpacking production dump to %s' % sqldump_file)
    local("gunzip -f %s" % sqldump_file_gz)

    print colors.green('Setting MySQL view permissions')
    local("mysql -u %s --password=%s -e \"grant all on informea.* to '%s'@'localhost' identified by '%s'\"" % (env.local.db_user, env.local.db_password, env.local.db_user, env.local.db_password))

    print colors.green('Loading production dump to MySQL (%s)' % env.local.db_database)
    local("cat %s | mysql -u %s --password=%s %s" % (sqldump_file, env.local.db_user, env.local.db_password, env.local.db_database))

    # Fix SQL database variables in WordPress
    print colors.green('Fixing WordpRess config table')
    local("mysql -u %s --password=%s -e \"update wp_options set option_value='%s' where option_name in ('home', 'siteurl')\" %s" % (env.local.db_user, env.local.db_password, env.local.url, env.local.db_database))

    # Cleanup temporary files
    print colors.green('Cleaning up ...')
    local("rm %s" % sqldump_file)

    print colors.red("Don't forget to disable CSS & JavaScript optimizations from back-end")


@_setup
def clonedb2production():
    """ Clone SQL database from production to local
    """
    if not hasattr(env, 'local'):
        print colors.red('Usage fab s:prod clonedb')
        return

    sqldump_file_gz = "/tmp/informea_dump.sql.gz"
    sqldump_file = "/tmp/informea_dump.sql"

    #print colors.green('Dumping local database to %s' % sqldump_file_gz)
    #local("mysqldump -u %s --password=%s %s | gzip > %s" % (env.local.db_user, env.local.db_password, env.local.db_database, sqldump_file_gz))

    #print colors.green('Uploading production dump to %s' % sqldump_file_gz)
    #put(sqldump_file_gz, sqldump_file_gz)
    #local("rm %s" % sqldump_file_gz)

    #print colors.green('Unpacking remote dump to %s' % sqldump_file)
    #sudo("gunzip -f %s" % sqldump_file_gz)

    print colors.green('Loading remote dump to MySQL (%s)' % env.db_database)
    sudo("cat %s | mysql -u %s --password=%s %s" % (sqldump_file, env.db_user, env.db_password, env.db_database))

    # Fix SQL database variables in WordPress
    print colors.green('Fixing WordpRess config table on production')
    sudo("mysql -u %s --password=%s -e \"update wp_options set option_value='%s' where option_name in ('home', 'siteurl')\" %s" % (env.db_user, env.db_password, env.url, env.db_database))

    # Cleanup temporary files
    print colors.green('Cleaning up ...')
    sudo("rm %s" % sqldump_file)


@_setup
def shell():
    """ Open a remote shell into drupal installation directory
    """

    from fabric.operations import open_shell
    with cd(env.root):
        open_shell("cd %s" % env.root)

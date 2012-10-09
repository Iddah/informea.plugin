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
from datetime import datetime
from fabric.api import *
from fabric.utils import puts
from fabric import colors
import fabric.network
import fabric.state

cfg = {
    'prod' : {
        'host'          : 'www.informea.org',
        'root'          : '/var/local/www.informea.org',
        'url'           : 'http://www.informea.org',
        'user'          : 'cristiroma',
        'db_user'       : 'informea',
        'db_password'   : 'dFzQksRAXYqWc1lOPsVd9i',
        'db_database'   : 'informea',
        'basedir'       : '/var/local/',
        'backupdir'     : '/home/cristiroma/sql-backups'
    },
    'local' : {
            'host'          : 'localhost',
            'root'          : '/Users/cristiroma/Work/informea/www',
            'url'           : 'http://informea.localhost',
            'db_user'       : 'root',
            'db_password'   : 'root',
            'db_database'   : 'informea'
    }
}


def _rec_setup(ob, properties):
    for key, value in properties.items():
        if isinstance(value, dict):
            v = fabric.state._AttributeDict()
            setattr(ob, key, v)
            _rec_setup(v, value)
        else:
            setattr(ob, key, value)

    env.hosts.append(env.prod.host)
    env.user = env.prod.user

_rec_setup(env, cfg)

#
# Tasks
#

def prod_db_backup():
    """ Backup the remote production database
    """
    filename = os.path.join(env.prod.backupdir,  '%s.sql.gz' % datetime.now().strftime("%Y%m%d"))
    print colors.green('Backup production database to %s' % filename)
    run("mysqldump -u %s --password=%s %s | gzip > %s" % (env.prod.db_user, env.prod.db_password, env.prod.db_database, filename))


def prod_update():
    """ Update code on the production server
    """
    with cd(env.prod.root):
        sudo("git pull origin master")
        sudo("./compress.sh css")
        sudo("./compress.sh js")


def clone_to_local():
    """ Clone production database to local
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


def prod_update_wordpress():
    """ Update WordPress on production. Install into another base
        directory on production then manually move the synmlink to point
        to this new directory.
    """
    # 1. Create target folder
    new_root = os.path.join(env.prod.basedir,  'www.informea.org.%s' % datetime.now().strftime("%Y%m%d"))
    sudo('rm -rf %s' % new_root)

    # 2. Clone the repository there
    sudo('git clone /var/local/git/www.informea.org %s' % new_root)

    # 3. Download and unpack latest wordpress
    with cd(new_root):
        sudo('wget http://wordpress.org/latest.tar.gz')
        sudo('tar zxf latest.tar.gz')
        sudo('rm -rf latest.tar.gz')

        for f in ['wordpress/wp-content', 'wordpress/wp-config-sample.php', 'wordpress/readme.html', 'wordpress/license.txt']:
            sudo('rm -rf %s' % f)
        sudo('mv wordpress/* .')
        sudo('rm -rf wordpress')
        sudo('chown root:root * -R')
        sudo('cp %s .' % os.path.join(env.prod.root, 'wp-config.php'))
        sudo('chown root:apache wp-config.php')
        sudo('chmod 440 wp-config.php')

        # 4. Create symlinks to the dynamic resources
        resdir = 'www.informea.org.resources'
        for symlink in ['api-data', 'clickheat', 'uploads',
                'wp-content/uploads',
                'wp-content/plugins/informea/gis/countries.map']:
            sudo('ln -s %s %s' % (os.path.join(env.prod.basedir, resdir, symlink), symlink))

        # 5. Minimize JS/CSS
        sudo('./compress.sh js')
        sudo('./compress.sh css')

    print colors.green('New InforMEA ready @: %s' % new_root)


def shell():
    """ Open a remote shell on production
    """

    from fabric.operations import open_shell
    with cd(env.root):
        open_shell("cd %s" % env.root)

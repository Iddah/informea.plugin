<?php
class ImportRamsarCSV extends imea_page_base_page {

    public $sites = array();

    function open_file() {
        if ($_FILES['file']['error'] == 0) {
            $filename = $_FILES['file']['tmp_name'];
            return $this->utf8_fopen_read($filename);
        }
        return null;
    }

    function parse() {
        set_time_limit(2000);
        global $wpdb;
        $ret = array();
        $handle = $this->open_file();
        if ($handle) {
            $row = 0;
            $countries = $this->get_countries();
            error_log("Starting Ramsar sites processing ...");
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                if ($row > 1) {
                    $site_id = $this->find_site_id_from_ref($data[0]);
                    $ob = new StdClass();
                    $ob->id = $data[4];
                    $ob->original_id = 'ramsar-' . $site_id;
                    $ob->country = $data[2];
                    $ob->latitude = $data[9];
                    $ob->longitude = $data[10];
                    $ob->name = $data[1];
                    $ob->id_country = array_key_exists(strtoupper($data[2]), $countries) ? $countries[strtoupper($data[2])] : 'ERROR';
                    $ob->url = 'http://www.wetlands.org/reports/spec.cfm?site_id=' . $site_id;
                    $ret[] = $ob;
                }
                $row++;
                if ($row % 100 == 0) {
                    error_log("Processed $row sites ...");
                }
            }
            fclose($handle);
        }
        return $ret;
    }

    function find_site_id_from_ref($ref) {
        $url = 'http://www.wetlands.org/reports/index.cfm?siteref=' . $ref;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $out = @curl_exec($ch);
        curl_close($ch);
        preg_match('/directory.cfm\?site_id=(\d+)/', $out, $matches);
        return $matches[1];
    }

    function test() {
        $this->actioned = true;
        $sites = $this->parse();
        $this->sites = $sites;
        $this->success = !empty($sites);
    }

    function import() {
        global $wpdb;
        global $current_user;
        $user = $current_user->user_login;
        $now_date = date('Y-m-d H:i:s', strtotime("now"));

        $this->actioned = true;
        $sites = $this->parse();
        // Insert or update the sites
        $existing = $this->get_existing_sites();
        foreach ($sites as $site) {
            if (array_key_exists($site->original_id, $existing)) {
                echo "Updating: {$site->original_id} <br />";
                $s = $existing[$site->original_id];
                $id = $s->id;
                // Update
                $wpdb->update('ai_country_site', array(
                        'id_country' => $site->id_country,
                        'id_treaty' => 18,
                        'name' => $site->name,
                        'url' => $site->url,
                        'latitude' => $site->latitude,
                        'longitude' => $site->longitude,
                        'rec_updated' => $now_date,
                        'rec_updated_author' => $user),
                    array('id' => $id)
                );
            } else {
                echo "Inserting: {$site->original_id} <br />";
                // Insert
                $wpdb->insert('ai_country_site', array(
                        'id_country' => $site->id_country,
                        'original_id' => $site->original_id,
                        'id_treaty' => 18,
                        'name' => $site->name,
                        'url' => $site->url,
                        'latitude' => $site->latitude,
                        'longitude' => $site->longitude,
                        'rec_created' => $now_date,
                        'rec_author' => $user)
                );
            }
        }
        echo '<h1>SUCCESS!!!!!</h1>';
    }


    function get_existing_sites() {
        global $wpdb;
        $ret = array();
        $sites = $wpdb->get_results('SELECT * FROM ai_country_site WHERE id_treaty = 18');
        foreach ($sites as $site) {
            if (!empty($site->original_id)) {
                $ret[$site->original_id] = $site;
            }
        }
        return $ret;
    }


    function get_countries() {
        global $wpdb;
        $ret = array();
        $countryOb = new imea_countries_page(null);
        $countries = $countryOb->get_countries();
        $byName = array();
        foreach ($countries as $country) {
            $ret[strtoupper($country->name)] = $country->id;
            $ret[strtoupper($country->long_name)] = $country->id;
            $byName[strtoupper($country->name)] = $country->id;
        }
        // Adjustments to the algorithm with some mappings
        $ret['KOREA, REPUBLIC OF'] = $ret[strtoupper('Republic of Korea')];
        $ret['IRAN, ISLAMIC REPUBLIC OF'] = $ret[strtoupper('Iran')];
        $ret['TRINIDAD & TOBAGO'] = $ret[strtoupper('Trinidad and Tobago')];
        $ret['MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF'] = $ret[strtoupper('Macedonia')];
        $ret['CONGO, DEMOCRATIC REPUBLIC OF'] = $ret[strtoupper('Democratic Republic of the Congo')];
        $ret['TANZANIA, UNITED REPUBLIC OF'] = $ret[strtoupper('Tanzania')];
        $ret['LIBYAN ARAB JAMAHIRIYA'] = $ret[strtoupper('Libya')];
        $ret['MOLDOVA, REPUBLIC OF'] = $ret[strtoupper('Moldova')];
        $ret['LAO PEOPLES DEMOCRATIC REPUBLIC'] = $ret[strtoupper('Lao People\'s Democratic Republic')];
        $ret['COTE DIVOIRE'] = $ret[strtoupper('Côte d\'Ivoire')];
        return $ret;
    }


    function utf8_fopen_read($fileName) {
        //$fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName));
        $fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName));
        $handle = fopen("php://memory", "rw");
        fwrite($handle, $fc);
        fseek($handle, 0);
        return $handle;
    }
}


class ImportWHCXML extends ImportRamsarCSV {

    function open_file() {
        $ret = null;
        if ($_FILES['file']['error'] == 0) {
            $filename = $_FILES['file']['tmp_name'];
            $f = fopen($filename, 'r');
            $ret = fread($f, filesize($filename));
            fclose($f);
        }
        return $ret;
    }


    function get_existing_sites() {
        global $wpdb;
        $ret = array();
        $sites = $wpdb->get_results('SELECT * FROM ai_country_site WHERE id_treaty = 16');
        foreach ($sites as $site) {
            if (!empty($site->original_id)) {
                $ret[$site->original_id] = $site;
            }
        }
        return $ret;
    }

    function parse() {
        set_time_limit(2000);
        global $wpdb;
        $ret = $arr = array();
        $xml = $this->open_file();
        if (!empty($xml)) {
            $d = new DOMDocument();
            $d->strictErrorChecking = false;
            $d->recover = true;
            libxml_use_internal_errors(true);
            $d->loadXML($xml);
            libxml_use_internal_errors(false);
            foreach ($d->getElementsByTagName('row') as $row) {
                $site = array();
                $site['original_id'] = null;
                $site['id_country'] = null;
                $site['id_treaty'] = 16;
                $site['name'] = null;
                $site['url'] = null;
                $site['latitude'] = null;
                $site['longitude'] = null;
                foreach ($row->childNodes as $tag) {
                    switch ($tag->tagName) {
                        case 'id_number':
                            $site['original_id'] = $tag->textContent;
                            break;
                        case 'iso_code':
                            $site['id_country'] = $tag->textContent;
                            break;
                        case 'site':
                            $site['name'] = $tag->textContent;
                            break;
                        case 'http_url':
                            $site['url'] = $tag->textContent;
                            break;
                        case 'latitude':
                            $site['latitude'] = $tag->textContent;
                            break;
                        case 'longitude':
                            $site['longitude'] = $tag->textContent;
                            break;
                    }
                }
                $arr[$site['original_id']] = $site;
            }
        }
        echo "<p>Individual sites found: " . count($arr) . "</p><hr />";

        // De-duplicate the sites by country (one site in many countries)
        $arr2 = array();
        foreach ($arr as $id => $site) {
            $codes = explode(',', $site['id_country']);
            if (count($codes) > 1) {
                $ob = $site;
                $id = $site['original_id'];
                unset($ret[$id]);
                foreach ($codes as $code) {
                    $new = $ob;
                    $new_id = $id . '_' . $code;
                    $new['original_id'] = 'manual_' . $new_id;
                    $new['id_country'] = $code;
                    $arr2[$new_id] = $new;
                }
            } else {
                $new_id = 'manual_' . $id . '_' . $code;
                $site['original_id'] = $new_id;
                $arr2[$new_id] = $site;
            }
        }

        // Remove sites from countries outside InforMEA coverage
        $countryOb = new imea_countries_page();
        $countries = $countryOb->get_countries();
        $codes = array();
        $ids = array();
        foreach ($countries as $country) {
            $codes[] = strtolower($country->code2l);
            $ids[strtolower($country->code2l)] = $country->id;
        }

        foreach ($arr2 as $id => $site) {
            if (in_array(strtolower($site['id_country']), $codes)) {
                $site['id_country'] = $ids[$site['id_country']];
                $ret[$id] = $site;
            } else {
                echo "Excluding site {$site['original_id']} from {$site['id_country']}<hr />";
            }
        }

        echo "<p>Distinct site-country found: " . count($ret) . "</p><hr />";
        $this->sites = $ret;
        return $ret;
    }

    function test() {
        $this->actioned = true;
        $sites = $this->parse();
        $this->sites = $sites;
        $this->success = !empty($sites);
    }

    function import() {
        global $wpdb;
        global $current_user;
        $user = $current_user->user_login;
        $now_date = date('Y-m-d H:i:s', strtotime("now"));

        $this->actioned = true;
        $sites = $this->parse();
        $this->sites = $sites;
        $this->success = !empty($sites);
        $existing = $this->get_existing_sites();
        foreach ($sites as $site) {
            if (array_key_exists($site['original_id'], $existing)) {
                echo "Updating: {$site['original_id']} <br />";
                $s = $existing[$site['original_id']];
                $id = $s->id;
                // Update
                $site['rec_updated'] = $now_date;
                $site['rec_updated_author'] = $user;
                $wpdb->update('ai_country_site', $site, array('id' => $id));
            } else {
                echo "Inserting: {$site['original_id']} <br />";
                // Insert
                $site['rec_created'] = $now_date;
                $site['rec_author'] = $user;
                $wpdb->insert('ai_country_site', $site);
            }
        }
        echo '<h1>SUCCESS!!!!!</h1>';
    }
}

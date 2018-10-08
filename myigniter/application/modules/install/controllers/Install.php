<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Install extends MX_Controller
{
    const TEMPLATE_PATH = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR;
    const TEMPLATE_NEW = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'new_install' . DIRECTORY_SEPARATOR;

    const MI_CONF = self::TEMPLATE_PATH . 'myigniter.txt';
    const DB_CONF = self::TEMPLATE_PATH . 'database.txt';
    const RO_CONF = self::TEMPLATE_PATH . 'routes.txt';
    const MG_CONF = self::TEMPLATE_PATH . 'migration.txt';
    const MG_MODULE_CONF = self::TEMPLATE_PATH . 'module' . DIRECTORY_SEPARATOR . 'migration.php';

    const MI_NEW = self::TEMPLATE_NEW . 'myigniter.txt';
    const DB_NEW = self::TEMPLATE_NEW . 'database.txt';
    const RO_NEW = self::TEMPLATE_NEW . 'routes.txt';
    const MG_NEW = self::TEMPLATE_NEW . 'migration.txt';
    const MG_MODULE_NEW = self::TEMPLATE_NEW . 'module' . DIRECTORY_SEPARATOR . 'migration.php';

    private $modules = [
        'aws3' => 'Amazon Web Service S3',
        'blog' => 'Blog starter',
        'media' => 'Media Library'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
    }

    public function index()
    {
        $this->reset();
        $data['modules'] = $this->modules;

        $this->load->view('index', $data);
    }

    public function do_install()
    {
        $this->load->library('form_validation');
        $response = new stdClass();

        if (!$this->input->is_ajax_request()) {
            $this->output->set_content_type('application/json')->set_output(json_encode(false));
        }

        $site_name = $this->input->post('site_name');
        $hostname = $this->input->post('hostname');
        $driver = $this->input->post('driver');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $db_name = $this->input->post('db_name');

        $this->form_validation->set_rules('site_name', 'Site name', 'required|trim|max_length[50]|min_length[1]');
        $this->form_validation->set_rules('hostname', 'Host name', 'required|trim|valid_url|max_length[50]|min_length[3]');
        $this->form_validation->set_rules('driver', 'Mysql driver', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('username', 'Mysql username', 'required|trim');
        $this->form_validation->set_rules('password', 'Mysql user password', 'trim');
        $this->form_validation->set_rules('db_name', 'Mysql database name', 'required|trim');

        if ($this->form_validation->run() == false) {
            $response->state = '0';
            $response->msg = validation_errors();
            $this->output->set_content_type('application/json')->set_output(json_encode($response));
        } else {
            // myigniter.php
            $replacement = [];
            $replacement[] = [
                'search' => '##SITE_NAME##',
                'replace' => $site['name']
            ];
            $mi = $this->writeFile(self::MI_CONF, $replacement, 'config' . DIRECTORY_SEPARATOR . 'myigniter.php');

            // database.php
            $replacement = [];
            $replacement = [
                ['search' => '##HOSTNAME##', 'replace' => $hostname],
                ['search' => '##USERNAME##', 'replace' => $username],
                ['search' => '##PASSWORD##', 'replace' => $password],
                ['search' => '##DATABASE##', 'replace' => $db_name],
                ['search' => '##DRIVER##', 'replace' => $driver]
            ];
            $db = $this->writeFile(self::DB_CONF, $replacement, 'config' . DIRECTORY_SEPARATOR . 'database.php');

            // myigniter.php
            $replacement = [];
            $replacement[] = [
            'search' => '##SITE_NAME##',
            'replace' => $site_name
            ];
            $mi = $this->writeFile(self::MI_CONF, $replacement, 'config' . DIRECTORY_SEPARATOR . 'myigniter.php');
            
            // routes.php
            $ro = $this->writeFile(self::RO_CONF, [], 'config' . DIRECTORY_SEPARATOR . 'routes.php');

            if ($mi && $db && $ro) {
                $response->state = '1';
                $response->msg = 'success';
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            } else {
                $this->reset();
                $response->state = '0';
                $response->msg = 'We found some error happens while try to install, please recheck your website information and try again';
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
        }
    }

    public function runMigration()
    {
        $response = new stdClass();
        $mg = false;

        if ($this->db->initialize()) {
            // Migration
            $this->load->library('migration');
            if ($this->migration->latest()) {
                $mg = $this->writeFile(self::MG_CONF, [], 'config' . DIRECTORY_SEPARATOR . 'migration.php');
                $moduleInstall = $this->modules($this->input->post('module'));
                if ($moduleInstall) {
                    $this->removeModule('install');
                    foreach ($this->modules as $key => $value) {
                        if ($this->input->post('module')) {
                            if (!in_array($key, $this->input->post('module'))) {
                                $this->removeModule($key);
                            }
                        } else {
                            $this->removeModule($key);
                        }
                    }
                }
            }
        }

        if ($mg) {
            $response->state = '1';
            $response->msg = 'success';
        } else {
            $this->reset();
            $response->state = '0';
            $response->msg = 'We found some error happens while try to install, please recheck your website information and try again';
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function modules($listModules)
    {
        $migrate = true;
        if ($listModules) {
            foreach ($this->modules as $key => $value) {
                if (in_array($key, $listModules)) {
                    if ($this->migration->init_module($key)) {
                        $migrate = $this->migration->latest();
                        if (!$migrate) {
                            return false;
                        }
                        $this->writeFile(self::MG_MODULE_CONF, [], 'modules' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'migration.php');
                    } else {
                        return false;
                    }
                }
            }
        }
        return $migrate;
    }

    public function reset()
    {
        $mi = $this->writeFile(self::MI_NEW, [], 'config' . DIRECTORY_SEPARATOR . 'myigniter.php');
        $db = $this->writeFile(self::DB_NEW, [], 'config' . DIRECTORY_SEPARATOR . 'database.php');
        $ro = $this->writeFile(self::RO_NEW, [], 'config' . DIRECTORY_SEPARATOR . 'routes.php');
        $mg = $this->writeFile(self::MG_NEW, [], 'config' . DIRECTORY_SEPARATOR . 'migration.php');
        foreach ($this->modules as $key => $value) {
            $mg = $this->writeFile(self::MG_MODULE_NEW, [], 'modules' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'migration.php');
        }
    }

    public function removeModule($moduleName)
    {
        $dir = APPPATH . 'modules' . DIRECTORY_SEPARATOR . $moduleName;
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    private function writeFile($file = null, $replacement = [], $target)
    {
        if (read_file($file)) {
            $code = read_file($file);
            foreach ($replacement as $key => $value) {
                $code = str_replace($value['search'], $value['replace'], $code);
            }
            if (write_file(APPPATH . $target, $code)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

/* End of file install.php */
/* Location: ./application/controllers/install.php */

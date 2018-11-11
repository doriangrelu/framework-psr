<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 13/05/2018
 * Time: 11:00
 */

namespace App\Framework\Console;


use App\Framework\Event\DoubleEventException;
use Cake\Utility\Inflector;
use Framework\App;

class Commands
{
    private $method;
    private $params;
    private $root;

    private $app;

    private const DS = DIRECTORY_SEPARATOR;
    private const CONTROLLER = "Controllers" . self::DS;
    private const MODELS = "";

    public function __construct(App $app, array $matches)
    {
        $this->app = $app;
        $this->method = $matches[2];
        $this->params = $matches[3];
        $this->root = dirname(dirname(__DIR__)) . self::DS;
    }

    private function controller()
    {


        die('here');
    }

    public function models($name)
    {

    }

    /**
     * @throws \Exception
     */
    public function addSeed()
    {
        $classSeed = $bar = <<<EOT
class 
EOT;
        $seedDirectory = $this->app->getContainer()->get("seed");
        $nb = count(glob($seedDirectory . '*.php')) + 1;
        $fileName = mb_strtoupper(Inflector::slug($nb . '-' . $this->params)) . ".php";
        if (file_exists($fileName)) {
            throw new \Exception("Seeds already exist");
        }
        file_put_contents($seedDirectory . $fileName, "<?php //Class HERE");
    }

    public function runSeeds()
    {

    }


    public function run()
    {
        $method = $this->method;
        $this->$method();
    }
}
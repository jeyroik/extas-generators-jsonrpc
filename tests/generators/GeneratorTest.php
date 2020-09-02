<?php
namespace tests;

use extas\components\console\TSnuffConsole;
use extas\components\generators\jsonrpc\ByDocComment;
use extas\components\generators\jsonrpc\ByDynamicPlugins;
use extas\components\generators\jsonrpc\ByInstallSection;
use extas\components\generators\JsonRpcGenerator;
use extas\components\plugins\TSnuffPlugins;
use extas\interfaces\stages\IStageDynamicPluginsPrepared;
use tests\generators\misc\InstallSomething;
use tests\generators\misc\OperationWithDocComment;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use tests\generators\misc\PreparedPlugin;
use tests\generators\misc\SomeEntity;

/**
 * Class GeneratorTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class GeneratorTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffPlugins;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffPlugins();
    }

    public function testGenerateByPluginInstallDefault()
    {
        $generator = new ByInstallSection([
            ByInstallSection::FIELD__INPUT => $this->getTestInput(),
            ByInstallSection::FIELD__OUTPUT => $this->getOutput()
        ]);

        $plugins = [new InstallSomething()];
        $result = $generator->generate($plugins);

        $mustBe = include getcwd() . '/tests/specs.php';
        $this->assertEquals(
            $mustBe,
            $result[JsonRpcGenerator::FIELD__OPERATIONS],
            'Current: ' . print_r($result, true)
        );
    }

    public function testGenerateByDocComment()
    {
        $generator = new ByDocComment([
            ByDocComment::FIELD__INPUT => $this->getTestInput(),
            ByDocComment::FIELD__OUTPUT => $this->getOutput()
        ]);

        $plugins = [new OperationWithDocComment()];
        $result = $generator->generate($plugins);
        $mustBe = include getcwd() . '/tests/specs.comments.php';

        $this->assertEquals(
            $mustBe,
            $result[JsonRpcGenerator::FIELD__OPERATIONS],
            'Current: ' . print_r($result, true)
        );
    }

    public function testGenerateByDynamicPlugins()
    {
        $generator = new ByDynamicPlugins([
            ByDocComment::FIELD__INPUT => $this->getTestInput(),
            ByDocComment::FIELD__OUTPUT => $this->getOutput()
        ]);

        $this->createSnuffPlugin(PreparedPlugin::class, [IStageDynamicPluginsPrepared::NAME]);

        $result = $generator->generate([
            [
                'repository' => 'snuffRepository',
                'item_class' => SomeEntity::class,
                'entity_name' => 'snuff item'
            ]
        ]);

        $mustBe = include getcwd() . '/tests/specs.dyn.php';

        $this->assertEquals(
            $mustBe,
            $result[JsonRpcGenerator::FIELD__OPERATIONS],
            'Incorrect result: ' . print_r($result, true)
        );
    }

    /**
     * @param string $prefix
     * @param string $path
     * @return InputInterface
     */
    protected function getTestInput(
        string $prefix = 'PluginInstallJson',
        string $path = '/tests/generated.specs.json'
    ): InputInterface
    {
        return $this->getInput([
            'prefix' => $prefix,
            'filter' => '',
            'specs'=> getcwd() . $path,
            'only-edge' => false
        ]);
    }
}

<?php
namespace tests;

use extas\components\console\TSnuffConsole;
use extas\components\generators\jsonrpc\ByDocComment;
use extas\components\generators\jsonrpc\ByInstallSection;
use extas\components\generators\JsonRpcGenerator;
use extas\components\repositories\TSnuffRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class GeneratorTest
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class GeneratorTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    protected function tearDown(): void
    {
    }

    public function testGenerateByPluginInstallDefault()
    {
        $generator = new ByInstallSection([
            ByInstallSection::FIELD__INPUT => $this->getTestInput(),
            ByInstallSection::FIELD__OUTPUT => $this->getOutput()
        ]);

        $plugins = [new InstallSomething()];
        $result = $generator->generate($plugins);

        $mustBe = include 'specs.php';
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
        $mustBe = include 'specs.comments.php';

        $this->assertEquals(
            $mustBe,
            $result[JsonRpcGenerator::FIELD__OPERATIONS],
            'Current: ' . print_r($result, true)
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

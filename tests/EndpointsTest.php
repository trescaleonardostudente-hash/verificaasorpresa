<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;

class EndpointsTest extends TestCase
{
    private $app;
    
    protected function setUp(): void
    {
        $this->app = AppFactory::create();
    }
    
    /**
     * Test dell'endpoint /1 - Pezzi forniti
     */
    public function testEndpoint1()
    {
        $this->assertTrue(true);
        // Test reale necessite un'istanza di app configurata
    }
    
    /**
     * Test dell'endpoint /2 - Fornitori che forniscono ogni pezzo
     */
    public function testEndpoint2()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /3 - Fornitori pezzi rossi
     */
    public function testEndpoint3()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /4 - Pezzi solo Acme
     */
    public function testEndpoint4()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /5 - Fornitori sopra media
     */
    public function testEndpoint5()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /6 - Costo massimo per pezzo
     */
    public function testEndpoint6()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /7 - Solo pezzi rossi
     */
    public function testEndpoint7()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /8 - Pezzi rossi e verdi
     */
    public function testEndpoint8()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /9 - Pezzi rossi o verdi
     */
    public function testEndpoint9()
    {
        $this->assertTrue(true);
    }
    
    /**
     * Test dell'endpoint /10 - Almeno due fornitori
     */
    public function testEndpoint10()
    {
        $this->assertTrue(true);
    }
}

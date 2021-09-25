<?PHP
namespace RatchetChat\Entrypoint;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use RatchetChat\App;

class EventHub implements MessageComponentInterface 
{
    protected $connections;
    protected $authenticator;
    protected $app;
    public $di;

    public function __construct() 
    {
        $this->app = new App();
        $this->app->logger->info("Arranca el Hub ");
    }

    public function onOpen(ConnectionInterface $newConn) 
    {
        try
        {            
            $this->app->HandleOpen($newConn);
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) 
    {
        try 
        {
            $this->app->HandleMessage($from, $msg);
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }

    }

    public function onClose(ConnectionInterface $conn) 
    {
        try 
        {
            $this->app->HandleClose($conn);
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }

    }

    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        try 
        {
            echo "An error has occurred: {$e->getMessage()} on connection {$conn->resourceId}\n";

            $conn->close();
        } 
        catch (\Exception $ex) 
        {
            throw $ex;
        }

    }
}
<?PHP
namespace RatchetChat\Presentation;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use RatchetChat\App;
use RatchetChat\Auth\Authenticator;

class EventHub implements MessageComponentInterface 
{
    protected $connections;
    protected $authenticator;
    protected $app;

    public function __construct() 
    {
        $this->connections = new \SplObjectStorage;
        $this->app = new App();
        $this->authenticator = new Authenticator();
    }

    public function onOpen(ConnectionInterface $newConn) 
    {
        try
        {            
            $this->authenticator->Authenticate($newConn);
            $this->app->logger->info("Se agregó un nuevo cliente con id de resource: $newConn->resourceId");
            $this->connections->attach($newConn);
            echo "New connection! ({$newConn->resourceId})\n\n";
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
            $objMsg = json_decode($msg, true);

            $this->app->HandleMessage($from, $objMsg);
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
            $this->connections->detach($conn);

            echo "Connection {$conn->resourceId} has disconnected*************************************************\n\n";
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
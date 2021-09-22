<?PHP

namespace RatchetChat\Middleware;

class Auth
{

    public function __construct(){

        
    }

    public function Authenticate($newClient)
    {
        return true;
        
    }
}
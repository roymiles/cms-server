<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RandomFakeDataController extends Controller
{   
    /**
     * @Route("/faker/users/", name="AddRandomFakeUsers")
     */
    public function fakeUsersAction(Request $request)
    {
        $fakerManager = $this->get('app.FakerManager');
        $apiUserManager = $this->get('app.ApiUserManager');
        if($fakerManager->isEnabled){
          for($i = 0; $i < $fakerManager->maxEntries; $i++){
            $faker = Faker\Factory::create();
            $apiUserManager->add($faker->username, $faker->email, $faker->password);
          }
          return '1';
        }else{
          return '0';
        }
    }
}

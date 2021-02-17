<?php
require 'config.php';

//FIRST, INITIALIZE LIST OF PRODUCTS ON THE PAGE
//putting them in separate arrays makes it a little cleaner in the long run

class Items
{
    private array $list = [];

    public function __construct()
    {
    }

    public function AddItem(string $name, float $price)
    {
        array_push($this->list, ['name' => $name, 'price' => $price]);
    }


    public function GetPrice(int $key){
        return $this->list[$key]['price'];
    }

    public function GetItems() : array
    {
        return $this->list;
    }

    public function ResetAmounts()
    {
        foreach($this->list as &$item){
            $item['amount'] = 0;
        }
    }
}

$drinksCls = new Items();
$drinksCls->AddItem('Cola', 2);
$drinksCls->AddItem('Fanta', 2);
$drinksCls->AddItem('Sprite', 2);
$drinksCls->AddItem('Ice-tea', 3);

$foodCls = new Items();
$foodCls->AddItem('Club Ham', 3.20);
$foodCls->AddItem('Club Cheese', 3);
$foodCls->AddItem('Club Cheese & Ham', 4);
$foodCls->AddItem('Club Chicken', 4);
$foodCls->AddItem('Club Salmon', 5);

//later on, we can parse through $products to calculate what has been ordered and how much it cost.
//this way of doings things should prevent abuse through adjusting HTML

//INIT CONST VALUES
const NORMAL_DELIVERY_TIME = 2 * 60;    //time in minutes
const EXPRESS_DELIVERY_TIME = 45;       //time in minutes
const EXPRESS_DELIVERY_COST = 5;        //cost in euros. you can't fool me.
const COOKIE_NAME = "totalSpentAmount"; //name to use in the cookie
CONST DRINKS_PAGE = "drinks";           //unless I get enums this is how we'll handle pages
CONST FOOD_PAGE = "foodstuffs";         //ditto

define("OWNERS_MAIL", "bossMail@thisServer.derp");

//INIT ADDRESS FORM VALUES
$email = $street = $streetNr = $city = $zipCode = "";
$emailErr = $streetErr = $streetNrErr = $cityErr = $zipCodeErr = $orderErr = "";
$currentPage = 0;
$drinksAmount = $foodAmount = [];
$expressDelivery = false;
$totalValue = 0;
$totalSpentValue = 0;

//STUFF FOR MAILS

//HANDLE FORM & ADDRESS INPUT
$isFormOkay = true;
$isFormSent = false;
$confirmationMessage = "";

//
if (!isset($_GET["food"]) || $_GET["food"] != 0)
{
    $products = $foodCls->GetItems();
    $currentPage = FOOD_PAGE;
}
else
{
    $products = $drinksCls->GetItems();
    $currentPage = DRINKS_PAGE;
}

//HANDLE POST DATA
if (!empty($_POST))
{
    //store POST data
    $postData = $_POST;
    //validate inputs and use appropriately
    //VALIDATE EMAIL
    if (empty($postData["email"]))
    {
        $emailErr = "Email is required!";
        $isFormOkay = false;
        $email = $_SESSION["email"];
    }
    else
    {
        $_SESSION["email"] = $email = cleanseInput($postData["email"]);

        //validate if email is actually a valid email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $emailErr = ("email address is invalid!");
            $isFormOkay = false;
        }
    }

    //VALIDATE STREET
    if (empty($postData["street"]))
    {
        $streetErr = "Street is required!";
        $isFormOkay = false;
        $street = $_SESSION["street"];
    }
    else
    {
        $_SESSION["street"] = $street = cleanseInput($postData["street"]);

    }

    //VALIDATE STREET NUMBER
    if (empty($postData["streetnumber"]))
    {
        $streetNrErr = "Street number is required!";
        $isFormOkay = false;
        $streetNr = $_SESSION["streetNr"];
    }
    else
    {
        //validate if streetnumber is a number
        $_SESSION["streetNr"] = $streetNr = cleanseInput($postData["streetnumber"]);
        if (!is_numeric($streetNr))
        {
            $streetNrErr = ("street number is not a valid number!");
            $isFormOkay = false;
        }
    }

    //VALIDATE CITY
    if (empty($postData["city"]))
    {
        $cityErr = "City is required!";
        $isFormOkay = false;
        $city = $_SESSION["city"];
    }
    else
    {
        $_SESSION["city"] = $city = cleanseInput($postData["city"]);
    }

    //VALIDATE ZIP CODE
    if (empty($postData["zipcode"]))
    {
        $zipCodeErr = "Zip code is required!";
        $isFormOkay = false;
        $zipCode = $_SESSION["zipCode"];
    }
    else
    {
        //validate if zip code is a number
        $_SESSION["zipCode"] = $zipCode = cleanseInput($postData["zipcode"]);
        if (!is_numeric($zipCode))
        {
            $zipCodeErr = "streetnumber is not a valid number!";
            $isFormOkay = false;
        }

    }

    //CHECK & VALIDATE PRODUCTS
    //$data["products"] returns either null or an array of checked items
    if (isset($postData["products"]))
    {
        $_SESSION[$currentPage] = $postData["products"];

        //clamp negative values
        foreach($_SESSION[$currentPage] as &$product)
        {
            $product = max(0,$product);
        }

        //calculate total value of order
        //calculate the value of the drinks AND the food
        foreach($_SESSION[DRINKS_PAGE] as $i => $item)
        {
            $totalValue += $drinksCls->GetPrice($i) * (int)$item;
        }
        foreach($_SESSION[FOOD_PAGE] as $i => $item)
        {
            $totalValue += $foodCls->GetPrice($i) * (int)$item;
        }

    }

    //HANDLE CONFIRMATION MESSAGE & CONFIRMATION EMAIL
    if ($isFormOkay && $totalValue >= 0)
    {
        //check time to delivery and if it's express delivery, add cost to total value.
        $timeToDelivery = NORMAL_DELIVERY_TIME;
        if (isset($postData["express_delivery"]))
        {
            $timeToDelivery = EXPRESS_DELIVERY_TIME;
            $totalValue += EXPRESS_DELIVERY_COST;
        }
        $isFormSent = true;
        $confirmationMessage = "Your order has been successfully placed and will arrive in " . date('H:i', mkTime(0, $timeToDelivery)) . " hours";

        $message = "delivery address: \n" . $street . " " . $streetNr . "\n" . $city . " ZIP: " . $zipCode . "\n Order final cost: " . $totalValue;
        $message .= "estimated time of delivery in " . date('H:i', mkTime(0, $timeToDelivery)) . " hours";

        mail($email, "git yo sandwiches", $message);
        mail(OWNERS_MAIL, "someone ordered stuff", $message);

        //remove orders from session storage
//        unset($_SESSION[DRINKS_PAGE]);
//        unset($_SESSION[FOOD_PAGE]);
    };
}
else
{
    if (!empty($_SESSION["email"]))
    {
        $email = $_SESSION["email"];
    }
    if (!empty($_SESSION["street"]))
    {
        $street = $_SESSION["street"];
    }
    if (isset($_SESSION["streetNr"]))
    {
        $streetNr = $_SESSION["streetNr"];
    }
    if (isset($_SESSION["city"]))
    {
        $city = $_SESSION["city"];
    }
    if (isset($_SESSION["zipCode"]))
    {
        $zipCode = $_SESSION["zipCode"];
    }
}

//CREATE COOKIE
//initialize
if (isset($_COOKIE[COOKIE_NAME]))
{
    $totalSpentValue = $_COOKIE[COOKIE_NAME] + $totalValue;
//    echo $totalSpentValue;
}
else
{
//    echo "initializing cookie";
    $totalSpentValue += $totalValue;
}
setCookie(COOKIE_NAME, $totalSpentValue, time() + (86400 * 30), "/");

//delete cookie
//setCookie(COOKIE_NAME, "", time() - 3600);

//delete session
//session_destroy();

require 'form-view.php';
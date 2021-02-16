<?php
require 'config.php';

//FIRST, INITIALIZE LIST OF PRODUCTS ON THE PAGE
if (!isset($_GET["food"]) || $_GET["food"] == 1)
{
    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];
}
else
{
    $products = [
        ['name' => 'Cola', 'price' => 2],
        ['name' => 'Fanta', 'price' => 2],
        ['name' => 'Sprite', 'price' => 2],
        ['name' => 'Ice-tea', 'price' => 3],
    ];
}
//later on, we can parse through $products to calculate what has been ordered and how much it cost.
//this way of doings things should prevent abuse through adjusting HTML

//INIT CONST VALUES
const NORMAL_DELIVERY_TIME = 2 * 60;    //time in minutes
const EXPRESS_DELIVERY_TIME = 45;       //time in minutes
const EXPRESS_DELIVERY_COST = 5;        //cost in euros. you can't fool me.

//INIT ADDRESS FORM VALUES
$email = $street = $streetNr = $city = $zipCode = "";
$emailErr = $streetErr = $streetNrErr = $cityErr = $zipCodeErr = "";
$expressDelivery = false;
$totalValue = 0;

//HANDLE FORM & ADDRESS INPUT
$isFormOkay = true;
$isFormSent = false;
$confirmationMessage = "";

if (!empty($_POST))
{
    //store POST data
    $data = $_POST;
    //validate inputs and use appropriately

    //VALIDATE EMAIL
    if (empty($data["email"]))
    {
        $emailErr = "Email is required!";
        $isFormOkay = false;
        $email = $_SESSION["email"];
    }
    else
    {
        $_SESSION["email"] = $email = cleanseInput($data["email"]);

        //validate if email is actually a valid email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $emailErr = ("email address is invalid!");
            $isFormOkay = false;
        }
    }

    //VALIDATE STREET
    if (empty($data["street"]))
    {
        $streetErr = "Street is required!";
        $isFormOkay = false;
        $street = $_SESSION["street"];
    }
    else
    {
        $_SESSION["street"] = $street = cleanseInput($data["street"]);

    }

    //VALIDATE STREET NUMBER
    if (empty($data["streetnumber"]))
    {
        $streetNrErr = "Street number is required!";
        $isFormOkay = false;
        $streetNr = $_SESSION["streetNr"];
    }
    else
    {
        //validate if streetnumber is a number
        $_SESSION["streetNr"] = $streetNr = cleanseInput($data["streetnumber"]);
        if (!is_numeric($streetNr))
        {
            $streetNrErr = ("street number is not a valid number!");
            $isFormOkay = false;
        }
    }

    //VALIDATE CITY
    if (empty($data["city"]))
    {
        $cityErr = "City is required!";
        $isFormOkay = false;
        $city = $_SESSION["city"];
    }
    else
    {
        $_SESSION["city"] = $city = cleanseInput($data["city"]);
    }

    //VALIDATE ZIP CODE
    if (empty($data["zipcode"]))
    {
        $zipCodeErr = "Zip code is required!";
        $isFormOkay = false;
        $zipCode = $_SESSION["zipCode"];
    }
    else
    {
        //validate if zip code is a number
        $_SESSION["zipCode"] = $zipCode = cleanseInput($data["zipcode"]);
        if (!is_numeric($zipCode))
        {
            $zipCodeErr = "streetnumber is not a valid number!";
            $isFormOkay = false;
        }

    }

    //CHECK & VALIDATE PRODUCTS
    //$data["products"] returns either null or an array of checked items

    if (isset($data["products"]))
    {
        foreach ($data["products"] as $key=>$item)
        {
//            echo($products[$key]['name']);
            $totalValue += $products[$key]['price'] * (int)$item;
        }
    }

    //HANDLE CONFIRMATION MESSAGE
    if ($isFormOkay)
    {
        $timeToDelivery = NORMAL_DELIVERY_TIME;
        if (isset($data["express_delivery"]))
        {
            $timeToDelivery = EXPRESS_DELIVERY_TIME;
            $totalValue += EXPRESS_DELIVERY_COST;
        }
        $isFormSent = true;
        $confirmationMessage = "Your order has been successfully placed and will arrive in " . date('H:i', mkTime(0, $timeToDelivery)) . " hours";
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





require 'form-view.php';
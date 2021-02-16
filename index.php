<?php
require 'config.php';

//store variables from POST method

//INIT FORM VALUES
$email = $street = $streetNr = $city = $zipCode = "";
//INIT FORM ERROR VALUES
$emailErr = $streetErr = $streetNrErr = $cityErr = $zipCodeErr = "";

$isFormOkay = true;
$isFormSent = false;

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
        $_SESSION["email"] = $email = $data["email"];

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
        $_SESSION["street"] = $street = $data["street"];

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
        $_SESSION["streetNr"] = $streetNr = $data["streetnumber"];
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
        $_SESSION["city"] = $city = $data["city"];
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
        $_SESSION["zipCode"] = $zipCode = $data["zipcode"];
        if (!is_numeric($zipCode))
        {
            $zipCodeErr = "streetnumber is not a valid number!";
            $isFormOkay = false;
        }

    }

    $isFormSent = $isFormOkay;
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

//your products with their price.
$products = [
    ['name' => 'Club Ham', 'price' => 3.20],
    ['name' => 'Club Cheese', 'price' => 3],
    ['name' => 'Club Cheese & Ham', 'price' => 4],
    ['name' => 'Club Chicken', 'price' => 4],
    ['name' => 'Club Salmon', 'price' => 5]
];

$products = [
    ['name' => 'Cola', 'price' => 2],
    ['name' => 'Fanta', 'price' => 2],
    ['name' => 'Sprite', 'price' => 2],
    ['name' => 'Ice-tea', 'price' => 3],
];

$totalValue = 0;


require 'form-view.php';
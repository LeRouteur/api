**Update A User**
----
Calling this service permits to update informations about a user.

**POST body in form-encoded format.**

**DATA NEEDS TO BE SERIALIZED BEFORE POST !**

* **URL**

  https://secureconnect.online/api/user/update

* **Method:**

  `POST`

* **Data Params:**

    *Key => Value*

    `lastname` => Doe

    `firstname` => John

    `mail` => test@example.com

    `sex` => man | woman | other

    `address` => Living Dead 3

    `city` => Zombotron
    
    `zip` => 1111
    
    `ldap_username` => johndoe
    
    `auth_token` => ageneratedtokenthatwillbegiventoyou123

* **Success Response:**

  * **Code:** 201 Created<br />
    **Content:** `{"success" : "User Updated"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"*value* Invalid"}` -> if there are errors while validating POST data, you will see into the *value* field the name of the invalid key.

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/user/update",
      dataType: "json",
      type : "POST",
      data: {lastname:"Doe",firstname:"John",mail:"test@example.com",sex:"man",address:"Living Dead 3",city:"Zombotron",zip:"111111",auth_token:"ageneratedtokenthatwillbegiventoyou123"},
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

    Data will be changed, but you need to send a new GET request to obtain the new data.
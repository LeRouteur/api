**Register A New User**
----
Calling this service permits to register a new user.

**POST body in form-encoded format.**

**DATA NEEDS TO BE SERIALIZED BEFORE POST !**

* **URL**

  https://secureconnect.online/api/users/add

* **Method:**
  
  /api/users/add

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

    `password` => SuperSecretP@ssword123

    `password_conf` => SuperSecretP@ssword123

* **Success Response:**

  * **Code:** 201 Created<br />
    **Content:** `{"success":"User Created"}`

  * **Code:** 200 OK<br/>
    **Content:** `{"error":"User Already Registered"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br/>
    **Content:** `{"error":"Password Does Not Match"}`

  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"*value* Invalid"}` -> if there are errors while validating POST data, you will see into the *value* field the name of the invalid key.

  * **Code:** 400 Bad Request<br />
    **Content:** `{"error":"Password Does Not Meet The Requirements"}`
    
* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/users/add",
      dataType: "json",
      type : "POST",
      data: {lastname:"Doe", firstname:"John", mail:"test@example.com", sex:"man", address:"Living Dead 3", city:"Zombotron", zip:"1111", password:"SuperSecr      etP@ssword123", password_conf:"SuperSecretP@ssword123"},
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

    A confirmation mail is sent to the user after the registration is successful. It contains a link to activate the account (check activation.md). A new account will also be created in the LDAP annuary.

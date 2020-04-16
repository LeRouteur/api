**Delete A User**
----
Calling this service permits to delete the specified user.

**POST body in form-encoded format.**

**DATA NEEDS TO BE SERIALIZED BEFORE POST !**

* **URL**

  https://secureconnect.online/api/user/delete

* **Method:**

  `POST`

* **Data Params:**

    *Key => Value*

    `username` => john.doe

    `auth_token` => ageneratedtokenthatwillbegiventoyou123

* **Success Response:**

  * **Code:** 200 OK<br/>
    **Content:** `{"success":"User Deleted"}`
 
* **Error Response:**

  * **Code:** 400 Bad Request<br/>
  **Content:** `{"error":"Bad Request"}`
  
  * **Code:** 400 Bad Request<br/>
  **Content:** `{"error":"User Does Not Exist"}`

  * **Code:** 500 Internal Server Error<br/>
    **Content:** `{"error":"Contact Administrator"}`

* **Sample Call:**

  ```javascript
    $.ajax({
      url: "https://secureconnect.online/api/user/delete",
      dataType: "json",
      type : "POST",
      data: {username:"john.doe", auth_token:"ageneratedtokenthatwillbegiventoyou123"},
      success : function(r) {
        console.log(r);
      }
    });
  ```

* **Notes:**

    This function deletes the user from the website database and from the LDAP annuary. Be careful when using it, there's
    no possibility to restore the user when deleted.
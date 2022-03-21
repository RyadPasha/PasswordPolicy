# PasswordPolicy
This class provides a password validation against the standard password requirements rules.

Rules
==========
* `minLength()`
* `maxLength()`
* `minDigit()`
* `minSpecialChar()`
* `minUpperCase()`
* `minLowerCase()`
* `occurrences()`
* `sequential()`
* `cantContain()`
* `blackList()`
* `notIn()`


Configuration
==========
Let's first create an instance of the class:

```php
include 'PasswordPolicy.php';

$policy = new PasswordPolicy();
```

Now set your own rules:
```php
$policyBuilder = $policy->minDigit(1)
                        ->minLength(8)
                        ->maxLength(30)
                        ->specialCharacter(1)
                        ->upperCase(2)
                        ->lowerCase(2)
                        ->occurrences(2)
                        ->sequential(3);
```

### Advanced
You can use constraints functions, which are:
* `cantContain()` // Checks if password has any part from the this array
* `blackList()`   // Checks if password is equal to any string in this array
* `notIn()`       // Checks if password is equal to any old passwords in this array

```php
$cantContain = [
  'f*ck',
];
$blackListed = [
  'abcd',
  'asd123'
];
$oldPasswords  = [ // Can be passed in hashed form (but you will have to pass the new password hash in `notIn($needle, $hashedPassword)`)
  'mohamed',
  'riyad',
];

$policyBuilder = $policy->cantContain($cantContain)
                        ->blackList($blackListed)
                        ->notIn($oldPasswords);
```

How to use
==========
You simply need to call the `checkPassword()` method, which returns either true or false;

If false, you may want to get errors and print them to the user from the `getErrors()` method:

```php
if ($policyBuilder->checkPassword('Zy1234f*ck')) {
  echo 'Password is OK!';
}

else {
  echo 'Errors found:';
  echo '<br />- ' . implode('<br />- ', $policyBuilder->getErrors());
}
```

^ Will print:
>Errors found:
>- Password must contain at least 2 lowercase characters
>- Password can not contain 3 sequentials letters or numbers
>- Password can not contain `f*ck`


### Contribute

* Fork the repo
* Create your branch
* Commit your changes
* Create a pull request

## Discussion
For any queries contact me at: **m@ryad.me**

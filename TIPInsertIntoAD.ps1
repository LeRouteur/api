Import-Module ActiveDirectory

$password = ConvertTo-SecureString "SecureConnect123" -AsPlainText -Force
$myCreds = New-Object System.Management.Automation.PSCredential("Administrateur", $password)
    $userFile = Get-Content -Path C:\Users\Cyril\Documents\api-sco\src\config\user.txt
if ($userFile.Count -eq 0) {
    # File is empty, we exit
    exit;
} else {
    foreach($line in $userFile) {
        $userData = $userFile.Split("{,}")
        $Name = $userData[0]
        New-ADUser -Name $userData[4] -Surname $userData[1] -SamAccountName $userData[2] -UserPrincipalName $userData[3] -GivenName $userData[0] -AccountPassword($userData[5] | ConvertTo-SecureString -AsPlainText -Force) -PostalCode $userData[6] -City $userData[7] -StreetAddress $userData[8] -Path "OU=Users-VPN,DC=secureconnect,DC=local" -Enabled $true -Credential $myCreds -Server "172.17.214.167"
    }
    # Clear the content of the file
    Clear-Content -Path C:\Users\Cyril\Documents\api-sco\src\config\user.txt -Force    
}

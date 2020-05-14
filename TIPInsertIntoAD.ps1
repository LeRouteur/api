Import-Module ActiveDirectory

$password = ConvertTo-SecureString "" -AsPlainText -Force
$myCreds = New-Object System.Management.Automation.PSCredential("", $password)

$userFile = Get-Content -Path "\\172.16.0.50\tip\user.txt"

if ($userFile.Count -eq 0) {
    # File is empty, we exit
    exit;
} else {
    foreach($line in $userFile) {
        $userData = $userFile.Split("{,}")
        New-ADUser -Name $userData[4] -Surname $userData[1] -SamAccountName $userData[2] -UserPrincipalName $userData[3] -GivenName $userData[0] -AccountPassword($userData[5] | ConvertTo-SecureString -AsPlainText -Force) -PostalCode $userData[6] -City $userData[7] -StreetAddress $userData[8] -Path "OU=Users-VPN,DC=secureconnect,DC=online" -Enabled $true -Credential $myCreds -Server "172.16.0.51"
        # Clear the content of the file
        Clear-Content -Path "\\172.16.0.50\tip\user.txt" -Force
    }
}

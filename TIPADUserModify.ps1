Import-Module ActiveDirectory

$password = ConvertTo-SecureString "7BdGYZ8A1vRZo6pH1PpF" -AsPlainText -Force
$myCreds = New-Object System.Management.Automation.PSCredential("cyril.service", $password)

$userFile = Get-Content -Path "\\172.16.0.50\tip\infos.txt"

if ($userFile.Count -eq 0) {
    # File is empty, we exit
    exit;
} else {
    foreach($line in $userFile) {
        $userData = $line.Split("{,}")
        Set-ADUser -Identity $userData[6] -GivenName $userData[0] -Surname $userData[1] -DisplayName $userData[2] -StreetAddress $userData[3] -PostalCode $userData[4] -City $userData[5] -Credential $myCreds -Server "172.16.0.51"
    }
    # Clear the content of the file
    Clear-Content -Path "\\172.16.0.50\tip\infos.txt" -Force
    exit;
}

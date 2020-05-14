Import-Module ActiveDirectory

$password = ConvertTo-SecureString "" -AsPlainText -Force
$myCreds = New-Object System.Management.Automation.PSCredential("", $password)

$groupFile = Get-Content -Path "\\172.16.0.50\tip\group.txt"

if ($groupFile.Count -eq 0) {
    exit;
} else {
    foreach($line in $groupFile) {
        $groupData = $groupFile.Split("{,}")
        Add-ADGroupMember -Identity $groupData[0] -Members $groupData[1] -Credential $myCreds -Server "172.16.0.51"
        Clear-Content -Path "\\172.16.0.50\tip\group.txt"
    }
}

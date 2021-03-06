<?php
if(isset($_SESSION['LOGIN']))
{
    if($_SESSION['LOGIN'] == 'true')
    {
        $dockerClient = new Docker();
        $allContainers = $dockerClient->getAllContainers();
        $currentContainer = NULL;

        foreach($allContainers as $thisContainer)
        {
            if($thisContainer['Id'] == $_SESSION['CONTAINER'] || $_SESSION['CONTAINER'] == ltrim($thisContainer['Names']['0'], '/'))
                $currentContainer = $thisContainer;
        }

        if($currentContainer == NULL)
        {
            die("unknown container");
        }

        $logOutput = $dockerClient->getContainerLogs($_SESSION['CONTAINER']);

        $HTML = new HTML();
        $HTML->setHTMLTitle(ltrim($currentContainer['Names']['0'], '/'));
        $HTML->importHTML("style/default/dashboard.html");

        $HTML->ReplaceLayoutInhalt("%%ContainerName%%", trim(ltrim($currentContainer['Names']['0'], '/'))); 
        $HTML->ReplaceLayoutInhalt("%%ContainerLogOutput%%", html_entity_decode(clean($logOutput))); 
        $HTML->ReplaceLayoutInhalt("%%STATUS%%", html_entity_decode($currentContainer['Status'])); 
        $HTML->ReplaceLayoutInhalt("%%APIKey%%", trim($currentContainer['Labels']['remotepass'])); 

        $HTML->build();
        echo $HTML->ausgabe();
    }else{
        die("Login is not valid!");
    }
}else{
    die("Access denied!");
}
?>
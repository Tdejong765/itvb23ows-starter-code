/* Requires the Docker Pipeline plugin */
pipeline {
    agent { dockerfile true }
    
    stages {

        stage('SonarQubeScanner'){
            steps{
                script {scannerHome = tool 'SonarQube'}
                withSonarQubeEnv('SonarQube'){
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=Thomas-OWS"
                }
            }
        }
    }
}
/* Requires the Docker Pipeline plugin */
pipeline {
    agent any 
    stages {
        stage('Build') {
            steps {
                sh 'php --version'
            }
        }
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
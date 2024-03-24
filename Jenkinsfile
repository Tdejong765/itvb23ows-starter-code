/* Requires the Docker Pipeline plugin */
pipeline {
    agent { docker { image 'php:8.2-apache' } }
    stages {
         stage('Initialize'){
            steps{
                script {dockerHome = tool 'myDocker' 
                env.PATH = "${dockerHome}/bin:${env.PATH}"
                }
            }
        }
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
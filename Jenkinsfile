/* Requires the Docker Pipeline plugin */
pipeline {
    agent any

    stages {

       stage('Build') {
            steps {
                sh 'docker-compose -f docker-compose.yml build'
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
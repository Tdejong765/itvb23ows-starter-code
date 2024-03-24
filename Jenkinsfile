/* Requires the Docker Pipeline plugin */
pipeline {
    agent { label 'dockerserver' }

    stages {

        stage('Docker node test') {
            agent {
                docker {
                // Set both label and image
                label 'dockerserver'
                image 'node:7-alpine'
                args '--name docker-node' // list any args
                }
            }
            steps {
                // Steps run in node:7-alpine docker container on docker agent
                sh 'node --version'
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
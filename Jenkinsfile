/* Requires the Docker Pipeline plugin */
pipeline {
    agent {
        docker { image 'node:20.11.1-alpine3.19' }
    }
    
    stages {

        stage('Build') {
            steps {
                // Use fully-specified path to docker-compose executable
                sh 'docker-compose up -d'
            }
        }


        stage('Run PHPUnit Tests') {
            steps {
                 sh 'docker-compose exec php-app vendor/bin/phpunit'
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
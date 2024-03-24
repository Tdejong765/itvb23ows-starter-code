/* Requires the Docker Pipeline plugin */
pipeline {
    agent any
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
                sh 'docker-compose build'
            }
        }

        stage('Run PHPUnit Tests') {
            steps {
                sh './vendor/bin/phpunit'
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
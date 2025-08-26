pipeline {
    agent {
        docker {
            image 'composer:2.7'   // Image officielle avec PHP + Composer
            args '-u root:root'    // Permet d’installer des paquets si besoin
        }
    }

    environment {
        SONARQUBE_SERVER = 'sonarqube'  // Nom configuré dans Jenkins
        DOCKER_IMAGE = 'evenmonet-app'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/SkanderKaraa/Evenmonet.git'
            }
        }

        stage('Install dependencies') {
            steps {
                sh 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Run tests') {
            steps {
                sh 'vendor/bin/phpunit --coverage-clover=coverage.xml || true'
            }
            post {
                always {
                    junit allowEmptyResults: true, testResults: '**/tests/report/*.xml'
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                withSonarQubeEnv("${SONARQUBE_SERVER}") {
                    sh """
                        sonar-scanner \
                        -Dsonar.projectKey=evenmonet \
                        -Dsonar.sources=src \
                        -Dsonar.php.coverage.reportPaths=coverage.xml \
                        -Dsonar.host.url=http://localhost:9000
                    """
                }
            }
        }

        stage('Build Docker Image') {
            steps {
                sh "docker build -t ${DOCKER_IMAGE}:latest ."
            }
        }

        stage('Deploy') {
            steps {
                sh """
                    docker stop evenmonet || true
                    docker rm evenmonet || true
                    docker run -d -p 8082:80 --name evenmonet ${DOCKER_IMAGE}:latest
                """
            }
        }
    }

    post {
        always {
            echo 'Pipeline terminée'
        }
        success {
            echo '✅ Déploiement réussi'
        }
        failure {
            echo '❌ Erreur dans la pipeline'
        }
    }
}

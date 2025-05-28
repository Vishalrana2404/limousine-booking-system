pipeline {
    agent any
    environment {
        Server_ip = credentials("limousinebookings_${env.BRANCH_NAME}_server_ip")
    }
    stages {
        stage("cleanup") {
            steps {
                deleteDir()
            }
        }
        stage('git clone') {
            steps {
                checkout scm
            }
        }
        stage('SonarQube Analysis') {
            when {
                branch 'dev'
            }
 			steps {
 				script {
 					scannerHome = tool 'Zapbuildsonarqube'
 				}
 				withSonarQubeEnv('sonar-global') {
 				  sh "${scannerHome}/bin/sonar-scanner \
 				  -D sonar.token=${SONAR_TOKEN} \
 				  -D sonar.projectKey=506-01 \
 				  -D sonar.sources=. \
 				  -D sonar.host.url=http://sonarqube.zapbuild.in:9000/"
 				}	
 			}
 			post {
                always {
                    script {
                        def sonarqubeUrl = env.getProperty('sonarqube_url_id')
                        
                        emailext subject: '[${JOB_NAME}] - Sonarqube Scanning',
                                  body: "Sonarqube scanning has been completed successfully. Click [here](${sonarqubeUrl}=506-01) to check status",
                                  to: 'sheetalpandhi@zapbuild.com',
                                  replyTo: 'pc@zapbuild.com',
                                  from: "sonarqube@zapbuild.com"
                    }
                }
            }
 		}
        
        stage("Deploy on Server") {
            steps {
                script {
                    if (env.BRANCH_NAME == 'dev') {
                        sh 'echo "Dev Deployment"'
                        sh 'ssh jenkins@$Server_ip "/var/www/html/limousine/deploy.sh" ${BRANCH_NAME}'
                    } else if (env.BRANCH_NAME == 'qa') {
                        sh 'echo "QA Deployment"'
                        sh 'ssh jenkins@$Server_ip "/var/www/html/limousine/deploy.sh" ${BRANCH_NAME}'
                    } else if (env.BRANCH_NAME == 'main') {
                        sh 'echo "Main Deployment will be started"'
                        sh 'ssh jenkins@$Server_ip "/var/www/html/limousine/deploy.sh" ${BRANCH_NAME}'
                    }
                }
            }
        }
    }
}


role :aws, 54.93.210.241

set :ssh_options, {
    user: 'root',
    auth_methods: %w(publickey),
    forward_agent: false,
    keys: %w{deploy/id_rsa_travis_enc}
}
set :branch, 'master'

set :deploy_via, :remote_cache
set :application, 'connect-four'
set :repo_url, 'git@github.com:audriusb/connect-four.git'
set :deploy_to, '/var/www'

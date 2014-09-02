# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

# read json box configs
proxy  = JSON.parse( IO.read('vagrant_boxes/haproxy.json') )
rabbit = JSON.parse( IO.read('vagrant_boxes/rabbitmq.json') )
celery = JSON.parse( IO.read('vagrant_boxes/celery.json') )

# define servers
servers = [
  { :hostname => 'rabbit0', :type => 'rabbit', :primary => false},
  { :hostname => 'rabbit1', :type => 'rabbit', :primary => false},
  { :hostname => 'rabbit2', :type => 'rabbit', :primary => false},
  { :hostname => 'celery0', :type => 'celery', :primary => false},
  { :hostname => 'proxy', :type => 'proxy', :primary => false}
]

# cookbook path
cookbooks_path = 'berks_cookbooks'

# update rabbit cluster disk nodes and proxy
rabbit['json']['rabbitmq']['cluster_disk_nodes'] = []
counter = 0
servers.each do |server|
  if server[:type] == 'rabbit'
    # dynamically add the rabbit servers to the cluster configuration
    rabbit['json']['rabbitmq']['cluster_disk_nodes'].push("rabbit@"+server[:hostname])

    # dynamically add rabbit servers to the haproxy config
    conf = "server " + server[:hostname] + " " + server[:hostname] + ":5671 check inter 5000"

    # is this the main server or a backup? (change this if you prefer round robin etc..)
    if counter == 0
      conf = conf + " downinter 500"
    else
      conf = conf + " backup"
    end

    proxy['json']['haproxy']['listeners']['listen']['rabbitcluster 0.0.0.0:5671'].push(conf)
    counter = counter + 1
  end
end

boxes = {
  'rabbit' => rabbit,
  'proxy' => proxy,
  'celery' => celery
}

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Setup hostmanager config to update the host files
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true
  config.vm.provision :hostmanager

  # Forward our SSH Keys into the VM
  config.ssh.forward_agent = true


  # Loop through all servers and configure them
  servers.each do |server|
    config.vm.define server[:hostname], primary: server[:primary] do |node_config|
      node_config.vm.box = boxes[server[:type]]['box']
      node_config.vm.box_url = boxes[server[:type]]['boxurl']
      node_config.vm.hostname = server[:hostname]
      node_config.vm.network :private_network, :auto_network => true
      node_config.hostmanager.aliases = server[:hostname]

      node_config.vm.provision :chef_solo do |chef|
        chef.json = boxes[server[:type]]['json']
        chef.cookbooks_path = [cookbooks_path]

        boxes[server[:type]]['recipes'].each do |recipe|
          chef.add_recipe recipe
        end
      end
    end
  end

  # vagrant trigger to get cookbooks and install them in
  # cookbook path
  [:up, :provision].each do |cmd|
    if File.exist?("#{cookbook_path}/Berksfile.lock") == true && FileUtils.compare_file('Berksfile.lock', "#{cookbooks_path}/Berksfile.lock2") == false
      config.trigger.before cmd, :stdout => true do
        info 'Cleaning cookbook directory'
        run "rm -rf #{cookbooks_path}"
        info 'Installing cookbook dependencies with berkshelf'
        run "berks vendor #{cookbooks_path}"
      end
    end
  end
end


terraform {
	required_providers {
		vultr = {
			source = "vultr/vultr"
			version = "2.21.0"
		}
	}
}

variable "vultr_api_key" {
	sensitive = true
	type = string
}

provider "vultr" {
	api_key = var.vultr_api_key
	rate_limit = 100
	retry_limit = 3
}

resource "vultr_ssh_key" "key" {
	name = "private-key"
	ssh_key = file("./keys/key.pub")
}

resource "vultr_instance" "dearbook001" {
	plan = "vcg-a100-2c-15g-10vram"
	region = "ewr"
	os_id = 1743
	label = "dearbook001"
	tags = [ "terraform" ]
	hostname = "dearbook001"
	enable_ipv6 = false
	disable_public_ipv4 = false
	backups = "disabled"
	ddos_protection = false
	activation_email = false
	firewall_group_id = vultr_firewall_group.firewall.id
	vpc2_ids = [ vultr_vpc2.network.id ]
	ssh_key_ids = [ vultr_ssh_key.key.id ]
}

resource "vultr_instance" "dearbook002" {
	plan = "vcg-a100-1c-12g-8vram"
	region = "ewr"
	os_id = 1743
	label = "dearbook002"
	tags = [ "terraform" ]
	hostname = "dearbook002"
	enable_ipv6 = false
	disable_public_ipv4 = false
	backups = "disabled"
	ddos_protection = false
	activation_email = false
	firewall_group_id = vultr_firewall_group.firewall.id
	vpc2_ids = [ vultr_vpc2.network.id ]
	ssh_key_ids = [ vultr_ssh_key.key.id ]
}

resource "vultr_container_registry" "registry" {
	name = "dearbook001"
	region = "ewr"
	plan = "start_up"
	public = false
}

output "dearbook001" { value = vultr_instance.dearbook001.main_ip }
output "dearbook002" { value = vultr_instance.dearbook002.main_ip }
output "registry" { value = vultr_container_registry.registry.urn }

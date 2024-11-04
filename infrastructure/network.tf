resource "vultr_firewall_group" "firewall" {
	description = "firewall"
}

resource "vultr_firewall_rule" "ssh" {
	firewall_group_id = vultr_firewall_group.firewall.id
	protocol = "tcp"
	ip_type = "v4"
	subnet = "0.0.0.0"
	subnet_size = 0
	port = "22"
	notes = "SSH"
}

resource "vultr_firewall_rule" "http" {
	firewall_group_id = vultr_firewall_group.firewall.id
	protocol = "tcp"
	ip_type = "v4"
	subnet = "0.0.0.0"
	subnet_size = 0
	port = "80"
	notes = "HTTP"
}

resource "vultr_firewall_rule" "https" {
	firewall_group_id = vultr_firewall_group.firewall.id
	protocol = "tcp"
	ip_type = "v4"
	subnet = "0.0.0.0"
	subnet_size = 0
	port = "443"
	notes = "HTTPs"
}

resource "vultr_firewall_rule" "communication" {
	firewall_group_id = vultr_firewall_group.firewall.id
	protocol = "tcp"
	ip_type = "v4"
	subnet = vultr_vpc2.network.ip_block
	subnet_size = 0
	port = "2377"
	notes = "TCP for communication with and between manager nodes"
}

resource "vultr_firewall_rule" "overlay_network_tcp" {
	firewall_group_id = vultr_firewall_group.firewall.id
	protocol = "tcp"
	ip_type = "v4"
	subnet = vultr_vpc2.network.ip_block
	subnet_size = 0
	port = "7946"
	notes = "TCP/UDP for overlay network node discovery"
}

resource "vultr_firewall_rule" "overlay_network_udp" {
	firewall_group_id = vultr_firewall_group.firewall.id
	protocol = "udp"
	ip_type = "v4"
	subnet = vultr_vpc2.network.ip_block
	subnet_size = 0
	port = "7946"
	notes = "TCP/UDP for overlay network node discovery"
}

resource "vultr_firewall_rule" "traffic" {
	firewall_group_id = vultr_firewall_group.firewall.id
	protocol = "udp"
	ip_type = "v4"
	subnet = vultr_vpc2.network.ip_block
	subnet_size = 0
	port = "4789"
	notes = "UDP (configurable) for overlay network traffic"
}

resource "vultr_vpc2" "network" {
	description = "internal network"
	region = "ewr"
}

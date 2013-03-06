#!/usr/bin/env ruby
  
# Simple Ruby XML Socket Server
#
# This is a a simple socket server implementation in ruby
# to communicate with flash clients via Flash XML Sockets.
# 
# The socket code is based on the tutorial
# "Sockets programming in Ruby"
# by M. Tim Jones (mtj@mtjones.com).
#
# Date:: Thu, 18 Jan 2008
# Author:: Sebastian Tschan, https://blueimp.net
# License:: GNU Affero General Public License


# Include socket library:
require 'socket'

# Socket Server class:
class SocketServer

  def initialize(config_file)
    # List of configuration settings:
    @config = Hash::new
    # Initialize default settings:
    initialize_default_properties
    if config_file
      # Load settings from configuration file:
      load_properties_from_file(config_file)
    end
    # Sockets list:
    @sockets = Array::new
    # Clients list:
    @clients = Hash::new
    # Initialize server socket:
    initialize_server_socket
    if @server_socket
      # Log server start (STDOUT.flush prevents output buffering):
      puts "#{Time.now}\tServer started on Port #{@config[:server_port].to_s} ..."; STDOUT.flush
      begin
        # Start the server:
        run
      rescue SignalException
        # Controlled stop:
      ensure
        for socket in @sockets
          if socket != @server_socket
            # Disconnect all clients:
            handle_client_disconnection(socket, false)
          end
        end
        @sockets = nil
        @clients = nil
        # Log server stop:
        puts "#{Time.now}\tServer stopped."; STDOUT.flush
      end
    end
  end

  def run
  	# Endless loop:
    while 1
  		# Blocking select call. The first three parameters are arrays of IO objects or nil.
      # The last parameter is to set a timeout in seconds to force select to return
      # if no event has occurred on any of the given IO object arrays.
      res = select(@sockets, nil, nil, nil)
  		if res != nil then
        # Iterate through the tagged read descriptors:
  			for socket in res[0]
          # Received a connect to the server socket:
  				if socket == @server_socket then
  					accept_new_connection
          else
            # Received something on a client socket:
  					if socket.eof? then
              # Handle client disconnection:
              handle_client_disconnection(socket)
  					else
              # Handle client input data:
              handle_client_input(socket.gets(@config[:eol]), socket)
  					end
  				end
  			end
  		end
  	end
  end

  private

  def initialize_default_properties
    # Server address (empty = bind to all available interfaces):
    @config[:server_address] = ''
    # Server port:
    @config[:server_port] = 1935
    # Comma-separated list of clients allowed to broadcast (allows all if empty):
    @config[:broadcast_clients] = ''
    # Defines if broadcast is sent to broadcasting client:
    @config[:broadcast_self] = false
    # Maximum number of clients (0 allows an unlimited number of clients):
    @config[:max_clients] = 0
    # Comma-separated list of domains from which downloaded Flash clients are allowed to connect (* allows all domains):
    @config[:allow_access_from] = '*'
    # Defines the policy-file-request string sent by Flash clients:
    @config[:policy_file_request] = '<policy-file-request/>'    
    # Defines the cross-domain-policy string sent to Flash clients as response to a policy-file-request:
    @config[:cross_domain_policy] = '<cross-domain-policy><allow-access-from domain="'+@config[:allow_access_from]+'" to-ports="'+@config[:server_port].to_s+'"/></cross-domain-policy>'
    # EOL (End Of Line) character used by Flash XML Socket communication:
    @config[:eol] = "\0"
  end

  def load_properties_from_file(config_file)
    # Open the config file and go through each line:
    File.open(config_file, 'r') do |file|
      file.read.each_line do |line|
        # Remove trailing whitespace from the line:
        line.strip!
        # Get the position of the first "=":
        i = line.index('=')
        # Check if line is not a comment and a valid property:
        if (!line.empty? && line[0] != ?# && i > 0)
          # Add the configuration option to the config hash:
          key = line[0..i - 1].strip
          value = line[i + 1..-1].strip
          # Enable boolean values:
          if value.eql?('false')
            @config[key.to_sym] = false
          elsif value.eql?('true')
            @config[key.to_sym] = true
          else
            @config[key.to_sym] = value
          end
        end
      end      
    end
    if @config[:eol].empty?
      # Use default EOL if configuration option is empty:
      @config[:eol] = $/
    end
  end

  def initialize_server_socket
    begin
      # The server socket, allowing connections from any interface and bound to the given port number:
      @server_socket = TCPServer.new(@config[:server_address], @config[:server_port].to_i)
      # Enable reuse of the server address (e.g. for rapid restarts of the server):
      @server_socket.setsockopt(Socket::SOL_SOCKET, Socket::SO_REUSEADDR, 1)
      # Add the server socket to the sockets list:
      @sockets.push(@server_socket)
    rescue Exception => error
      # Log initialization failure:
      puts "#{Time.now}\tFailed to initialize Server on Port #{@config[:server_port].to_s}: #{error}."; STDOUT.flush
    end
  end

  def accept_new_connection
    begin
      # Accept the client connection:
      socket = @server_socket.accept
      # Retrieve IP and Port:
      ip = socket.peeraddr[3]
      port = socket.peeraddr[1]
      # Check if we have reached the maximum number of connected clients (always accept the broadcast clients):
      if @config[:max_clients].to_i == 0 || @clients.size < @config[:max_clients].to_i || !@config[:broadcast_clients].empty? && @config[:broadcast_clients].include?(ip)
        # Add the accepted socket connection to the socket list:
        @sockets.push(socket)
        # Create a new Hash to store the client data:
        client = Hash::new
        client[:id] = "[#{ip}]:#{port}"
        # Check if the client is allowed to broadcast:
        if @config[:broadcast_clients].empty? || @config[:broadcast_clients].include?(ip)
          client[:allowed_to_broadcast] = true
        else
          client[:allowed_to_broadcast] = false
        end
        # Add the client to the clients list:       
        @clients[socket] = client
        # Log client connection and the number of connected clients:
        puts "#{Time.now}\t#{client[:id]} Connects\t(#{@clients.size} connected)"; STDOUT.flush
      else
        # Close the socket connection:
        socket.close
      end
    rescue
      # Client disconnected before the address information (IP, Port) could be retrieved.
    end
  end

  def handle_client_disconnection(socket, delete_socket=true)
    # Retrieve the client ID for the current socket:
    client_id = @clients[socket][:id]
    begin
      # Close the socket connection:
      socket.close
    rescue
      # Rescue if closing the socket fails
    end
    if delete_socket
      # Remove the socket from the sockets list:
      @sockets.delete(socket)
    end
    # Remove the client ID from the clients list:
    @clients.delete(socket)
    # Log client disconnection:
    puts "#{Time.now}\t#{client_id} Disconnects\t(#{@clients.size} connected)"; STDOUT.flush
  end

  def handle_client_input(str, client_socket)
    # The input string with the EOL removed:
    str_chomped = str.chomp(@config[:eol])
    # Check for policy-file-request from Flash client:
    if str_chomped.eql?(@config[:policy_file_request])
      begin
        # Write the cross-domain-policy to the Flash client:
        client_socket.write(@config[:cross_domain_policy]+@config[:eol])
      rescue
        # Rescue if writing to the socket fails
      end
      # Log unformatted (dump) policy-file-request:
      puts "#{Time.now}\t#{@clients[client_socket][:id]} #{str_chomped.dump}"; STDOUT.flush
    else
      # Check if the_client is allowed to broadcast:
      if @clients[client_socket][:allowed_to_broadcast]
        # Go through the sockets list:
        @sockets.each do |socket|
          # Skip the server socket and skip the the client socket if broadcast is not to be sent to self:
          if socket != @server_socket && (@config[:broadcast_self] || socket != client_socket)
            begin
              # Write the broadcast message on the socket connection:
              socket.write(str)
            rescue
              # Rescue if writing to the socket fails
            end
          end
        end
        # Log unformatted (dump) broadcast message (with EOL character stripped):
        puts "#{Time.now}\t#{@clients[client_socket][:id]} #{str_chomped.dump}"; STDOUT.flush
      end
    end
  end

end


# Start the socket server with the first command line argument as configuration file:
SocketServer.new($*[0])
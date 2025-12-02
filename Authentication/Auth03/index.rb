require 'sinatra/base'
require 'active_record'
require 'digest/md5'
require 'rack-session-sequel'

class Exercise < Sinatra::Base
  use Rack::Session::Sequel

  # Database name
  def self.db
    'pentesterlab' 
  end

  helpers do
    alias_method :h, :escape_html
  end

  # ActiveRecord configuration
  ActiveRecord::Base.configurations[self.db] = {
    :adapter  => "mysql2",
    :host     => "localhost",
    :username => "pentesterlab",
    :password => "pentesterlab",
    :database => Exercise.db
  }

  use Rack::Session::Sequel

  SEED = "REDACTED"

  # User Model
  class User < ActiveRecord::Base
    ActiveRecord::Base.establish_connection Exercise.db
  end

  # Initialize database & seed admin
  configure do
    ActiveRecord::Base.establish_connection Exercise.db

    unless ActiveRecord::Base.connection.table_exists?("users")
      ActiveRecord::Migration.class_eval do
        create_table "users" do |t|
          t.string :username
          t.string :password
        end
      end

      User.create(
        :username => 'admin',
        :password => Digest::MD5.hexdigest(SEED + "REDACTED" + SEED)
      )
    end
  end

  # Sinatra settings
  set :bind, '0.0.0.0'
  set :views, File.join(File.dirname(__FILE__), 'views')

  # ROUTES ---------------------------------------------------

  # Login page
  get '/' do
    if params['username'] && params['password']
      @user = User.where(
        :username => params['username'].to_s,
        :password => Digest::MD5.hexdigest(SEED + params['password'].to_s + SEED)
      ).first

      if @user
        session['user'] = @user.username
        return erb :index
      end

    elsif session['user']
      @user = User.find_by_username(session['user'])
      if @user
        return erb :index
      end
    end

    erb :login
  end

  # Signup form
  get '/signup' do
    erb :signup
  end

  # Handle signup
  get '/submit' do
    users = User.all

    if users.any? { |u| u.username == params[:username] }
      @message = "Error: user already exists"
      erb :signup
    else
      @user = User.create(
        :username => params[:username],
        :password => Digest::MD5.hexdigest(SEED + params[:password] + SEED)
      )
      session['user'] = @user.username
      redirect '/'
    end
  end

  # Logout
  get '/logout' do
    session.clear
    redirect '/'
  end

end

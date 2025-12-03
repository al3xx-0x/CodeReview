require 'sinatra/base'
require 'active_record'
require 'digest/md5'
require 'rack-session-sequel'

class Exercise < Sinatra::Base
  use Rack::Session::Sequel

  def self.db
    'pentesterlab'
  end

  helpers do
    #include Sinatra::Partials
    alias_method :h, :escape_html
  end

  ActiveRecord::Base.configurations[db] = {
    :adapter  => "mysql2",
    :host     => "localhost",
    :username => "pentesterlab",
    :password => "pentesterlab",
    :database => Exercise.db
  }

  use Rack::Session::Sequel
  SEED = "******************************"

  class User < ActiveRecord::Base
    establish_connection Exercise.db
  end

  configure {
    #recreate() if $dev
    ActiveRecord::Base.establish_connection Exercise.db

    unless ActiveRecord::Base.connection.table_exists?("users")
      ActiveRecord::Migration.class_eval do
        create_table "users" do |t|
          t.string :username
          t.string :password
          t.integer :admin
        end
      end
    end
  }

  set :bind, '0.0.0.0'
  set :views, File.join(File.dirname(__FILE__), 'views')

  get '/' do
    @user = User.find(session[:user]) if session[:user]
    erb :index
  end

  post "/signup" do
    @user = User.create(params[:user])
    session[:user] = @user
    redirect '/'
  end

  get "/logout" do
    session.clear
    redirect "/"
  end
end

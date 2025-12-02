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
    alias_method :h, :escape_html
  end

  ActiveRecord::Base.configurations[db] = {
    :adapter  => "mysql2",
    :host     => "localhost",
    :username => "pentesterlab",
    :password => "pentesterlab",
    :database => Exercise.db
  }

  SEED = "REDACTED"

  class User < ActiveRecord::Base
    ActiveRecord::Base.establish_connection Exercise.db
  end

  configure {
    ActiveRecord::Base.establish_connection Exercise.db

    unless ActiveRecord::Base.connection.table_exists?("users")
      ActiveRecord::Migration.class_eval do
        create_table "users" do |t|
          t.string :username
          t.string :password
        end
      end
    end

    User.create(
      :username => 'admin',
      :password => Digest::MD5.hexdigest(SEED + "REDACTED" + SEED)
    )
  }

  set :bind, '0.0.0.0'
  set :views, File.join(File.dirname(__FILE__), 'views')

  get '/' do
    if params['username'] && params['password']
      @user = User.where(
        :username => params['username'].to_s,
        :password => Digest::MD5.hexdigest(SEED + params['password'].to_s + SEED)
      ).first

      if @user
        session['user'] = @user.username
        return redirect '/'
      end

    elsif session['user']
      @user = User.find_by_username(session['user'])
      if @user
        return erb :index
      end
    end

    erb :login
  end

  get '/signup' do
    erb :signup
  end

  get '/submit' do
    users = User.all

    if users.select { |x| x.username.casecmp(params[:username]) == 0 }.size > 0
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

  get '/logout' do
    session.clear
    redirect '/'
  end
end

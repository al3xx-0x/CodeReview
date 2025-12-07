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

  use Rack::Session::Sequel

  SEED = "REDACTED"

  # ========================
  # Models
  # ========================

  class User < ActiveRecord::Base
    establish_connection Exercise.db
    has_many :infos
  end

  class Info < ActiveRecord::Base
    establish_connection Exercise.db
    belongs_to :user
  end

  # ========================
  # Database creation logic
  # ========================

  configure do
    ActiveRecord::Base.establish_connection Exercise.db

    # Create infos table
    unless ActiveRecord::Base.connection.table_exists?("infos")
      ActiveRecord::Migration.class_eval do
        create_table "infos" do |t|
          t.string  :title
          t.text    :details
          t.integer :user_id
        end
      end
    end

    # Create users table
    unless ActiveRecord::Base.connection.table_exists?("users")
      ActiveRecord::Migration.class_eval do
        create_table "users" do |t|
          t.string :username
          t.string :password
        end
      end
    end

    # ========================
    # Insert default users
    # ========================

    user1 = User.create(
      :username => 'user1',
      :password => Digest::MD5.hexdigest(SEED + "pentesterlab" + SEED)
    )

    user1.infos << Info.new(
      :title   => "Confidential user1",
      :details => "Do not share"
    )

    user1.infos << Info.new(
      :title   => "Confidential user1 (2)",
      :details => "Do not redistribute"
    )

    user2 = User.create(
      :username => 'user2',
      :password => Digest::MD5.hexdigest(SEED + "REDACTED" + SEED)
    )

    user2.infos << Info.new(
      :title   => "Confidential user2. The KEY is PTLAB_KEY",
      :details => "Do not share."
    )

    user2.infos << Info.new(
      :title   => "Confidential user2 (2). The KEY is PTLAB_KEY",
      :details => "Do not redistribute."
    )
  end

  set :bind, '0.0.0.0'
  set :views, File.join(File.dirname(__FILE__), 'views')

  # ========================
  # Routes
  # ========================

  get '/' do
    if params['username'] && params['password']
      @user = User.where(
        :username => params['username'].to_s,
        :password => Digest::MD5.hexdigest(SEED + params['password'] + SEED)
      ).first

      if @user
        session[:user] = @user
        @infos = @user.infos
        return erb :index
      end

    elsif session[:user]
      @user  = User.find(session[:user])
      @infos = @user.infos
      return erb :index
    end

    erb :login
  end

  get '/logout' do
    session.clear
    redirect '/'
  end

  get '/infos/:id' do
    if session[:user] && User.find(session[:user])
      @info = Info.find(params[:id].to_s)
      erb :info
    else
      session.clear
      redirect '/'
    end
  end
end

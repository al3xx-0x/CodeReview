require "sinatra"


# Endpoint `/` returns this source code, colorized
get "/" do
  require "rouge"

  content_type "text/html"

  code = File.read(__FILE__)
  formatter = Rouge::Formatters::HTML.new
  lexer = Rouge::Lexers::Ruby.new
  highlighted = formatter.format(lexer.lex(code))

  <<~HTML
    <html>
      <head>
        <style>
          body { background: #1e1e1e; color: #d4d4d4; font-family: monospace; padding: 20px; }
          pre { padding: 20px; border-radius: 8px; overflow-x: auto; }
          #{Rouge::Themes::MonokaiSublime.render(scope: '.highlight')}
        </style>
      </head>
      <body>
        <pre class="highlight">#{highlighted}</pre>
      </body>
    </html>
  HTML
end

# Endpoint `/read?filename=...`
get "/read" do
  filename = params["filename"]

  return "Missing filename" unless filename

  begin
    open(filename).read
  rescue => e
    status 400app
    "Error: #{e}"
  end
end

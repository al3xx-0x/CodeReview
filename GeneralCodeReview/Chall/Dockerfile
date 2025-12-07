FROM ruby:3.3

# Install Sinatra and the base image tools
RUN gem install sinatra && apt-get update && apt-get install -y gcc && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy files
COPY app.rb /app/
COPY readflag.c /app/
COPY flag /app/

# --- FLAG SETUP ---
# Flag owned by root only and only readable by root
RUN chown root:root /app/flag && chmod 600 /app/flag

# --- SUID BINARY SETUP ---
# 1. Compile the helper (runs as root inside container build process)
RUN gcc /app/readflag.c -o /app/readflag

RUN gem install rackup puma
RUN gem install rouge
# 2. Set ownership to root and add the SUID (Set User ID) bit
# The SUID bit (u+s) is the core of the challenge.
# This makes the binary run with the permissions of its owner (root).
RUN chown root:root /app/readflag && chmod u+s /app/readflag

# --- APP USER SETUP ---
# Create non-root user for the web app
RUN useradd -m ctf
USER ctf
# Expose port & run Sinatra app
EXPOSE 7777
CMD ["ruby", "app.rb", "-o", "0.0.0.0", "-p", "7777"]

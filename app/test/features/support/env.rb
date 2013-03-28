# IMPORTANT: This file is generated by cucumber-rails - edit at your own peril.
# It is recommended to regenerate this file in the future when you upgrade to a 
# newer version of cucumber-rails. Consider adding your own code to a new file 
# instead of editing this one. Cucumber will automatically load all features/**/*.rb
# files.
require 'cucumber/rails'
require 'active_support/time'
require 'rspec'
require 'rspec/rails'
require 'capybara/rails'
require 'capybara/rspec'
require 'savon'

Capybara.register_driver :chrome do |app|
  Capybara::Selenium::Driver.new(app, :browser => :chrome)
end
#Capybara.default_driver = :chrome
Capybara.javascript_driver = :chrome
Capybara.save_and_open_page_path = File.dirname(__FILE__) + '/../snapshots'
 Capybara.default_wait_time = 3

Capybara.configure do |config|
  config.run_server = false
  config.app_host   = 'http://lemontest.thetimebilling.com/feature_integracion-contable-gastos'
  config.server_host  = '/feature_integracion-contable-gastos'
end
# Capybara defaults to XPath selectors rather than Webrat's default of CSS3. In
# order to ease the transition to Capybara we set the default here. If you'd
# prefer to use XPath just remove this line and adjust any selectors in your
# steps to use the XPath syntax.
Capybara.default_selector = :css

 


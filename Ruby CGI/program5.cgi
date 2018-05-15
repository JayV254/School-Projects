#!/usr/bin/ruby


# Jesse Vaught CS316 Program 5 using Ruby cgi
# ********Using dog ate my program days*******

# Purpose: TO present user with a dynamic form built from the contents of the expected file "Sports.json" and output results based upon the form input
# Input:  Sports.json file and any resulting .json files needed to display results according to the form values
# Output: Result of search in valid HTML and the retun of the same form again so user can continue without page reload

# What did I implement?


# a) Your program runs without runtime errors and performs basic reporting on JSON objects properly. 65 pts
# ---------IMPLEMENTED---------

# b) Your program presents an HTML form that is properly presented with dynamically (ie, not static/hardcoded)populated fields and options from Sports.json file. 10 pts
# ----------IMPLEMENTED--------

# c) Your program is robust against missing or incorrect JSON fields/elements. 5 pts
# ----------IMPLEMENTED--------

# d) Your program uses Rails properly. 20 pts
# -------NOT IMPLEMENTED-------


# add required modules JSON and CGI
require 'json'
require 'cgi'

# initialize arrays to hold form values from sports.json
sport_array=[]
year_array=[]
searchterm_array=[]

# Read JSON from a file, iterate over objects
file = open("Sports.json")
json = file.read

parsed = JSON.parse(json)

# parse json and put elements in respective array container for outputting to form
parsed["sport"].each do |value|
  if !value["results"].empty?
    value["results"].each do |year,year_file|
      if !year_array.include? year
          year_array.push(year)
      end
    end
    # if the title is included push the title and then check for searchterm and push it as well
    if value.include? 'title'  
      sport_array.push(value["title"])
      if value.include? 'searchterms'
        value['searchterms'].each do |term|
          if !searchterm_array.include? term 
            searchterm_array.push(term)
          end
        end
      end
    end
  end
end


# output dynamic form using open select statement so user can see all valid choices at once
cgi = CGI.new("html4")
cgi.out {
   cgi.html {
      cgi.head { "\n"+cgi.title{"Jesse Vaught Program 5"} } +
      cgi.body { "\n"+
         cgi.form {"\n"+
            cgi.h1 { "Welcome to Fan Xelk!"} +
            cgi.h2 { "Make your selection and receive up to date sports statistics " } + "\n"+
            cgi.popup_menu("NAME" => "sport", "SIZE" => sport_array.length, "MULTIPLE" => true,
            "VALUES" => sport_array) +"\n"+
	    cgi.popup_menu("NAME" => "year", "SIZE" => year_array.length, "MULTIPLE" => true,
            "VALUES" => year_array) + "\n" +
	    cgi.popup_menu("NAME" => "searchterm", "SIZE" => searchterm_array.length, "MULTIPLE" => true,
            "VALUES" => searchterm_array) + "\n" +
            cgi.br +
            cgi.submit
         }
      }
   }
}

# initalize json variable to hold found json for corresponding year
result_json = ""

# loop json again and find result for search
if cgi.has_key? "sport" and cgi.has_key? "year"
  parsed["sport"].each do |entry|
    if entry["title"] == cgi["sport"]
      if entry["results"].has_key? cgi["year"]
        $result_json = entry["results"][cgi["year"]]
      else 
        puts "<p>Sorry we don't have information for " + cgi["sport"] + " during that time,  please try again!</p>"
      end
    end
  end
  if $result_json.empty?
    puts "<p>Sorry something went wrong, please try again!</p>"      
  end
else 
  puts "<p>Please input a year to search!</p>"
end  

# initalize wins and losses to be changed as array is traversed
$wins = 0
$losses = 0

# loop through result json unless it is empty
if !$result_json.to_s.empty?
  if File.file?($result_json)
    file = open($result_json)
    json = file.read
    parsed_result = JSON.parse(json)
# output comments if they are present in the result json file
    if parsed_result.has_key? "comments" and !parsed_result["comments"].empty?
      puts "<div style=\"text-align:center;border:3px solid black;width:50%;margin:auto\">"
      parsed_result["comments"].each do |comment|
        puts "<p style=\"text-align:center;font-size:20px\">" + comment + "</p>"
      end
      puts "</div><br>"
    else
      puts "<p style=\"text-align:center\">No Header Information Available</p>"
    end
    if parsed_result.has_key? "games" and !parsed_result["games"].empty?
      parsed_result["games"].each do |game|
        puts "<div style=\"text-align:center;border:2px solid black;width:40%;margin:auto\">"
        game.each do |category,value|
          if category == cgi["searchterm"]
            puts "<span style=\"font-size:20px;margin-top:-3px;margin-bottom:-3px;text-align:center;font-weight:bold\">" + category + ": " + value + "</span>"
          else
	    puts "<span style=\"margin-top:-3px;margin-bottom:-3px;text-align:center\">" + category + ": " + value + "</span>"
          end
# if category is winorlose increment the wins losses global variables accordingly
	  if category == "WinorLose"
	    if value == "W"
	      $wins += 1
	    else
	      $losses += 1
	    end
          end  
          puts "<br>"
        end
        puts "</div"
        puts "<br><br>"
      end
# finally output the wins and losses information after all game descriptions are displayed
      total_games = $wins + $losses
      wl_perc = $wins.to_f / total_games.to_f * 100 
      puts "<p style=\"text-align:center;border:2px solid green\">Win:Loss " + $wins.to_s + ":" + $losses.to_s + " Win Percentage: " + wl_perc.to_i.to_s + "%</p>"
    end
  else
     puts "<p>It seems we don't have the results for that year, please try again later!</p>"
  end
end  

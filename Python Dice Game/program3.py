#Jesse Vaught- jrva223@g.uky.edu
#Section 007
#References: CS115 Lecture Notes
#Purpose: To create a two-person dice game where individuals race to earn 100
# 	points before the other person.  Die are 6 sided and individuals take
#	turns rolling until either has won the round.
#Date: 30 March 2016
#Preconditions: Program is meant to run with two players, therefore two individual
#	inputs is reccomended.  Players must click to enter the game, while clicks
#	will also symbolize reocurring participation.  Participation is dependent
#	upon the individual players decision to continue rolling or end their round
#Postconditions: The program generates random numbers according to a die of sides 1-6.
#	Program keeps a running total of summed numerical value for each player
#	and outputs a message stating the the winning player if condition is achieved.
#Side Note: Debugger extremely helpful 

import random
from graphics import *

'''function name: instructions
purpose: to generate a graphics window that displays instructions for the game
pre-conditions: none
post-conditions: a graphics window containing text objects
design:
	-create window
        -draw window
        -draw text objects explaining the game
        -get mouse to close window
'''
def instructions():
    win = GraphWin("Instructions", 600, 600)
    win.setBackground("tan")
    header = Text(Point(300,50), "Instructions for Don't Roll ONE!")
    header.setSize(26)
    header.draw(win)
    line2_3 = Text(Point(300,100), "Player 1 goes first\nThen Player Two")
    line2_3.setSize(20)
    line2_3.draw(win)
    line4 = Text(Point(300,200), "Roll the die and get points as long as you dont roll a one!")
    line4.setSize(20)
    line4.draw(win)
    line5 = Text(Point(300,300), "You can choose to stop rolling at any time!")
    line5.setSize(20)
    line5.draw(win)
    line6 = Text(Point(300,400), "If you roll a one, you lose all points for your round!")
    line6.setSize(20)
    line6.draw(win)
    line7 = Text(Point(300,550), "First player to reach 100 wins!\n\n\nClick to Continue")
    line7.setSize(20)
    line7.draw(win)
    win.getMouse()
    win.close()
    return None



'''function name: draw_die
purpose: to draw a specific gif image based on parameter 1 
	(number of a die 1-6) and parameter 2 (Point(x and y)) for the center
        of the image.  The screen the image is drawn in depends on parameter 3
        (graphics window).
pre-conditions:  number of die, center point, graphics window
post-conditions: will output a graphics object of a die relative to the roll
	for that turn.
design:
	-(nest inside play_a_round loop)
        -call Image function with anchorpoint and file name of nth file selected
        -create graphics object of image
        
        return graphics object of gif image
        does affect graphics window by displaying correct die roll on screen
'''
def draw_die(die_num,center_point):
    die_num = str(die_num)
    die_image = Image(center_point, die_num + ".gif" )
    return die_image

'''function name: in_box
purpose:  to determine if the third parameter given (a point) is in the box 
	determined by the first two parameters given (two points). Returns True
        if it is in the box, False otherwise.
pre-conditions: Three point objects
post-conditions: True if the third point is in the box formed by the other two
	points, False otherwise.
design:
	-get x and y from first two points(corner1x,corner1y,corner2x,corner2y)
        -get x and y from third point (thirdx,thirdy)
        -if thirdx and thirdy are between coordinates of rectangle corners
        	result is True
        else
        	result is False
        
        return the result
        does not change the graphics display in any way
'''

def in_box(boxpt1, boxpt2, clickpt):
    result = False
    blcornerx = boxpt1.getX()
    blcornery = boxpt1.getY()
    urcornerx = boxpt2.getX()
    urcornery = boxpt2.getY()
    clickptx = clickpt.getX()
    clickpty = clickpt.getY()
    if blcornerx <= clickptx <= urcornerx and urcornery <= clickpty <= blcornery:
        result = True
    return result

'''function name: click_yes_or_no
purpose:  Prompts the player if they want to roll again.  Draws two clickable 
	buttons that allow users to decide to continue rolling the dice or not.
pre-conditions: Player number, window object
post-conditions: False if clicked in the no box, True if clicked in yes box
design:
	-create text object asking for player "" if they want to roll again
	-create two rectangle graphic objects
        -draw them
        -get corner points for each rectangle created
        	-get x and y for each point
        -get user mouse click point and store it in variable (choice_point)
        	-get x and y from point
	-call in_box with 2 corner pts from rectangle 1 and choice_point
        	if True
                	-return True
        -call in_box with 2 corner pts from rectangle 2 and choice_point
        	-if True 
                	-return False
        
        adds yes or no prompt to graphics window and asks to continue playing
        returns true or false depending on user click
        
'''

def click_yes_or_no(player_num, window_object):
    play_again = Text(Point(300,345), "Roll again, Player 1")
    yes_box = Rectangle(Point(80,100),Point(120,60))
    no_box = Rectangle(Point(80,150),Point(120,110))
    yes_box.draw(window_object)
    no_box.draw(window_object)
    yes = Text(Point(100,80), "Yes")
    yes.setSize(18)
    yes.draw(window_object)
    no = Text(Point(100, 130), "No")
    no.setSize(18)
    no.draw(window_object)
    user_choicept = window_object.getMouse()
    compare_yes = in_box(Point(80,100),Point(120,60),user_choicept)
    compare_no = in_box(Point(80,150),Point(120,110),user_choicept)
    while not compare_yes and not compare_no:
        not_valid = Text(Point(250, 170), "That's not a valid click")
        not_valid.setSize(18)
        not_valid.draw(window_object)
        user_choicept = window_object.getMouse()
        not_valid.undraw()
        flag = False
        compare_yes = in_box(Point(80,100),Point(120,60),user_choicept)
        compare_no = in_box(Point(80,150),Point(120,110),user_choicept)
    if compare_yes:	
        result = True
    else:
        result = False
    yes_box.undraw()
    no_box.undraw()
    yes.undraw()
    no.undraw()
    return result

'''function name: play_a_round
purpose: to show the roll, and calculate round total for parameter 1 
	(player 1 or 2) within a graphic object in parameter 2 (window object).
pre-conditions: chosen player, graphic window
post-conditions: Displays amount for each roll, and returns total turn amount.
design:
	-define round_tot as zero
        -define die_roll as random integer between 1 and 6
        -define round_flag as true
        -call click_yes_or_no
        -while round_flag == True or click_yes_or_no == False        	
        	-call draw_die with parameters (die_roll and position)
                -if die_roll == 1
                	-set round_flag to False
                        -set round_tot to 0
                -else
                	-add die_roll to round_tot and assign to round_tot
                -create graphics object showing the accumulated score per roll
                -get another die_roll with random integer 1-6
                -call click_yes_or_no
        -display graphic saying round is over and display round_tot
        	if round_tot > 0
                	-display round over and players total
                else
                	-display "you rolled a one :(" and round is over        
        -prompt user to click for next player roll (end round)
        
        return round_tot
        graphics window does change with each loop
                
'''

def play_a_round(player_num,window_object):
    round_tot = 0
    roundflag = True
    play_again = True
    flag_for1 = False
    die_roll = random.randrange(1,7)

# PUT IN MAIN FUNCTION
# round_graphic = Text(Point(350,200), "------Round for Player" , player_num,
#    "------")

    while roundflag != False and play_again:
        die_gif = draw_die(die_roll,Point(350,300))
        die_gif.draw(window_object)
        if die_roll == 1:
            roundflag = False
            round_tot1 = round_tot
            round_tot = 0
            flag_for1 = True
        else:
            round_tot += die_roll
            round_tot_graphic = Text(Point(350,500), "Player " + str(player_num) + " score for this round " + str(round_tot))
            round_tot_graphic.setSize(18)
            round_tot_graphic.draw(window_object)
            die_roll = random.randrange(1,7)
            roll_again = Text(Point(350,400),("Roll again, Player " + str(player_num) + "?"))
            roll_again.setSize(18)
            roll_again.draw(window_object)
            play_again = click_yes_or_no(player_num,window_object)
            die_gif.undraw()
            round_tot_graphic.undraw()
            roll_again.undraw()
    if flag_for1:
        round_over = Text(Point(350,400), "You rolled a ONE :(\nYou lost " + str(round_tot1) + " points")
        round_over.setSize(18)
        happy_round = Text(Point(0,0), "")
        
    else:
        round_totstr = str(round_tot)
        happy_round = Text(Point(350,300), "Good Round!")
        happy_round.setSize(30)
        happy_round.setTextColor("blue")
        happy_round.setStyle("bold")
        round_over = Text(Point(350,430), "You won " + round_totstr + " points")
        round_over.setSize(16)
    happy_round.draw(window_object)    
    round_over.draw(window_object)
    player_num = str(player_num)
    end_round = Text(Point(350,480), "End of round for Player" + player_num + "\n" + "Click to continue")
    end_round.setSize(20)
    end_round.draw(window_object)
    window_object.getMouse()
    if flag_for1:
    	die_gif.undraw()
    happy_round.undraw()
    end_round.undraw()
    round_over.undraw()
    return round_tot

'''function name: main
purpose: Encapsulate succeeding functions into an interactive game of dice
	in which players will provide input that will create output represented
	in a graphical window
pre-conditions: Initiate program
post-conditions: Graphics object is created stating the end result message and
	the program closes through user click.

design: 
	-set player1 total score and player2 total score to zeros (tot_score)
        -call function instructions to display new graphics window (win_instr)
        -get mouse to start player 1 turn
        -create new graphwin (win_round)
        -while tot_score1 < 100 or tot_score2 < 100
        	-call play_a_round
                -if play_a_round == 0
                	-call play_a_round for next player
        -display total points for both players, and tell them who won
        -getmouse to exit window and program.
        
        return a winning value and display the screen showing that value
        graphics window does change at end of game.
                
                	
'''

def main():
    instructions()
    win = GraphWin("play a round", 600,600)
    player1total = 0
    player2total = 0
    player_num = 1
    title = Text(Point(300,20),"Don't roll ONE!")
    title.setSize(20)
    title.draw(win)
    play = True
    while play:
        player1_graphic = Text(Point(450,50),"Player 1 Points: " + str(player1total))
        player1_graphic.setSize(18)
        player1_graphic.draw(win)
        player2_graphic = Text(Point(450,70),"Player 2 Points: " + str(player2total))
        player2_graphic.setSize(18)
        player2_graphic.draw(win)
        round_graphic = Text(Point(350,200), "------ Round for Player " + str(player_num) + "------")
        round_graphic.setSize(16)
        round_graphic.draw(win)        
        if player_num == 1:
            player1total += play_a_round(1,win)
            player_num += 1
        elif player_num == 2:
            player2total += play_a_round(2,win)
            player_num -= 1
        player1_graphic.undraw()
        player2_graphic.undraw()
        round_graphic.undraw()
        if player1total > 100:
            play = False
        elif player2total > 100:
            play = False        
    game_over = Text(Point(300,300),"Game over!")
    game_over.setSize(30)
    if player1total > player2total:
        message = Text(Point(300,100), "Congratulations Player 1!\n---You won with " + str(player1total) + " Points---")
    else:
        message = Text(Point(300,100), "Congratulations Player 2!\n---You won with " + str(player2total) + " Points---")
    player1_graphic.undraw()
    player2_graphic.undraw()
    round_graphic.undraw()
    message.setSize(30)
    message.setStyle("bold")
    message.setTextColor("green")
    win.close()    
    over_win = GraphWin("Game Over", 600,600)
    game_over.draw(over_win)
    message.draw(over_win)
    over_win.getMouse()
    message.undraw()
    game_over.undraw()
    over_win.close()
main()
    
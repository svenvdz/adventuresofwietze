<?php
	$username = "root";
	$password = "pass";
	$host = "localhost";
	$database = "jsgame";

	$link = mysqli_connect($host, $username, $password) or die("Error: database connection failed");

	mysqli_select_db($link, $database);

	$rimg = mysqli_query($link, "SELECT name, path FROM imgres");

	$imgres = "";

	while ($data = mysqli_fetch_array($rimg)) {
		$imgres .= "\r\n\t\t\t\t" . $data['name'] . " = loadImage(\"./assets/" . $data['path'] . "\");";
	}

	$raud = mysqli_query($link, "SELECT name, path FROM audres");

	$audres = "";

	while ($data = mysqli_fetch_array($raud)) {
		$audres .= "\r\n\t\t\t\t" . $data['name'] . " = loadSound(\"./assets/" . $data['path'] . "\");";
	}

	$rlvlscripts = mysqli_query($link, "SELECT * FROM levels");

	$lvlscript = "";
/*
			playerX = " . $data['playerSpawnX'] . ";
			playerY = " . $data['playerSpawnY'] . ";
*/
	while ($data = mysqli_fetch_array($rlvlscripts)) {
		$lvlnr = $data['id'];

		$renemies = mysqli_query($link, "SELECT x, y FROM enemies WHERE level=" . $lvlnr);

		$enemyscript = "";

		while ($data = mysqli_fetch_array($renemies)) {
			$enemyscript .= "\r\nimage(enemy, " . $data['x'] . " + levelZero + enemyPosition, " . $data['y'] . ");";
		}

		$lvlscript .= "
		\r\n
		if(currentLevel == " . $lvlnr . ") {
			if(!controlmenu) {
				if (keyIsDown(leftKey) && playerX > 27 && !leftCollision) {// && get(characterX-11, playerY + 20).toString != [0, 173, 28, 255]
					playerX-=3;
				} else if (keyIsDown(rightKey) && !rightCollision && playerX < ground.width - 427) {// && get(characterX+11, playerY + 20).toString != [0, 173, 28, 255]
					playerX+=3;
				}
				if((keyCode === 32 || keyCode === upKey) && jump < 24) {
						playerY-=9;
						jump++;
				}
			}
			if(playerX >= ground.width - 427) {
				background(gameover);
				if(keyCode === ENTER) {
					playerX = 250;
					playerY = 250;
				}
			} else if(playerY < 425) {
				background(0, 0, 0);
				color(0, 0, 255);
				if(enemyPosition > 200) {
					enemyRight = false;
				}
				if(enemyPosition < -200) {
					enemyRight = true;
				}
				if(enemyRight) {
					enemyPosition++;
				} else {
					enemyPosition--;
				}
				if(playerX < 400) {
					characterX = playerX;
					levelZero = 0;
					image(sky, 0, 0);
				} else {
					characterX = 400;
					levelZero = 400 - playerX;
					image(sky, 200 - playerX / 2, 0);
				}
				image(lvl" . $lvlnr . "ground, levelZero, 0);
				" . $enemyscript . "

				var grassColor = [0, 173, 28, 255];
				var dirtColor = [118, 58, 40, 255];
				var feetColor = get(characterX, playerY + 21);
				onGround = feetColor.toString() == grassColor || feetColor.toString() == dirtColor;
				var leftColor = get(characterX - 12, playerY + 19);
				leftCollision = leftColor.toString() == grassColor || leftColor.toString() == dirtColor;
				var rightColor = get(characterX + 11, playerY + 19);
				rightCollision = rightColor.toString() == grassColor || rightColor.toString() == dirtColor;
				if(!onGround) {
					playerY+=3;
				} else if(jump != 0) {
					jump = 0;
					keyCode = null;
				}
				image(wietze, characterX - 30, playerY - 20);
			} else {
				background(gameover);
				if(keyCode === ENTER) {
					playerX = 250;
					playerY = 250;
				}
			}
		}
		\r\n
		";
	}

	$script = "
			var fps = 0;
			var debugRowHeight = 20;
			var nextRow;

			var playMsc = true;
			var playSnd = true;
			var controlmenu = false;
			var mvolume = 10;
			var svolume = 10;
			var controlmode = 0;
			var currentState = 0;
			var currentChoice = 0;
			var currentLevel = 0;
			var selCol;
			var xSel;
			var ySel;

			var leftKey = 65;
			var rightKey = 68;
			var upKey = 87;
			var downKey = 83;

			var cloudX = 400;
			var cloudY = 40;
			var cloudTick = 0;
			var playerX = 0;
			var playerY = 0;
			var characterX;
			var characterY;
			var jump = 0;
			var levelZero = 0;
			var enemyPosition = 0;
			var enemyRight = 0;
			var onGround;
			var leftCollision = false;
			var rightCollision = false;

			function preload() {
				" . $audres . "
			}
			function setup() {
				createCanvas(1080, 608);
				" . $imgres . "
				$('img').remove();
				playMusic(menumsc);
			}
			function draw() {
				if(currentState == 0) {
					drawMenu();
				} else if(currentState == 1) {
					drawLevels();
				}  else if(currentState = 2) {
					if(currentLevel != 0) {
						" . $lvlscript . "
					}
				} else {
					currentState = 0;
				}
				if(controlmenu) {
					drawControlMenu();
				}
				if(getURLParams().debug != null) {
					drawDebugScreen(); ///////////////////////////////////////===========////////////////////////////////
				}
				if(getURLParams().debug == \"simple\" || getURLParams().debug == \"all\") {
					textSize(15);
					nextRow = 22;
					text(\"FPS: \" + fps, 10, nextRow);
					nextRow+=debugRowHeight;
					if(controlmode == 0) {
						text(\"Control mode: WASD\", 10, nextRow);
					} else {
						text(\"Control mode: Arrows\", 10, nextRow);
					}
					nextRow+=debugRowHeight;
					if(currentState == 2) {
						text(\"Level: \" + currentLevel, 10, nextRow);
						nextRow+=debugRowHeight;
					}
					if(getURLParams().debug == \"all\") {
						text(\"State: \" + currentState, 10, nextRow);
						nextRow+=debugRowHeight;
						if(currentState != 2) {
							text(\"Choice: \" + currentChoice, 10, nextRow);
							nextRow+=debugRowHeight;					
						}
						if(playMsc) {
							text(\"Music: on\", 10, nextRow);	
						} else {
							text(\"Music: off\", 10, nextRow);							
						}
						nextRow+=debugRowHeight;
						if(playSnd) {
							text(\"Sound: on\", 10, nextRow);
						} else {
							text(\"Sound: off\", 10, nextRow);
						}
						nextRow+=debugRowHeight;
						if(controlmenu) {
							text(\"Controlmenu: Shown\", 10, nextRow);
						} else {
							text(\"Controlmenu: Hidden\", 10, nextRow);
						}
						nextRow+=debugRowHeight;
						if(currentState == 0) {
							text(\"Cloud position: \" + cloudX, 10, nextRow);
							nextRow+=debugRowHeight;
						}
					}
					fill(0, 0, 0, 31);
					noStroke();
					rect(5, 5, 160, nextRow - 15);
				}
			}

			function drawMenu() {
				background(bgmenu);
				image(mncloud, cloudX, cloudY);
				translate(727, 158);
				rotate(radians(cloudX / 1427 * 360));
				image(mnsun, -301, -248);
				rotate(radians(-cloudX / 1427 * 360));
				translate(-727, -158);
				image(mntitle, 569, 145);
				if(currentChoice == 0) {
					image(mnplaysel, 602, 285);
					image(mnquit, 602, 353);
				} else if(currentChoice == 1) {
					image(mnplay, 602, 285);
					image(mnquitsel, 602, 353);
				} else {
					image(mnplay, 602, 285);
					image(mnquit, 602, 353);
				}
				if(playMsc) {
					image(mnsound, 603, 423);
				} else {
					image(mnsoundsel, 603, 423);
				}
				if(playSnd) {
					image(mnfx, 695, 423);
				} else {
					image(mnfxsel, 695, 423);
				}
				if(!controlmenu) {
					image(mncontrol, 786, 423);
				}
				if(cloudX > 1080) {
					cloudX = -347;
					cloudY = random(60);
				}
				if(cloudTick == 2) {
					cloudX++;
					cloudTick = 0;
				}
				cloudTick++;
				
			}

			function drawLevels() {
				selCol = (currentChoice - 1);
				xSel = selCol * 60 % 300 + 564;
				ySel = floor(selCol / 5) * 66 + 300;
				background(bgmenu);
				if(currentChoice == 0) {
					image(opmenu, 0, 0);
				} else {
					image(lvlselector, xSel, ySel);
				}
			}

			function drawControlMenu() {
				image(mncontrolsel, 786, 423);
				if(controlmode == 0) {
					image(mncarrow, 975, 433);
					image(mncwasdsel, 875, 433);
					leftKey = 65;
					rightKey = 68;
					upKey = 87;
					downKey = 83;
				} else if(controlmode == 1) {
					image(mncarrowsel, 975, 433);
					image(mncwasd, 875, 433);
					leftKey = LEFT_ARROW;
					rightKey = RIGHT_ARROW;
					upKey = UP_ARROW;
					downKey = DOWN_ARROW;
				}
			}

			function drawDebugScreen() {

			}

			function updateFPS(){
				fps = parseInt(frameRate());
			}
			setInterval(function(){
				updateFPS()
			}, 1000)

			function keyPressed() {
				if (keyCode === 90) {
					playMsc = !playMsc;
					if(currentState == 0 || currentState == 1) {
						if(playMsc) {
							playMusic(menumsc);
						} else {
							menumsc.stop();
						}
					}
				} else if (keyCode === 88) {
					playSnd = !playSnd;
				} else if(controlmenu) {
					if(keyCode === 87 || keyCode === 65 || keyCode === 83 || keyCode === 68) {
						controlmode = 0;
					} else if(keyCode === UP_ARROW || keyCode === RIGHT_ARROW || keyCode === LEFT_ARROW || keyCode === DOWN_ARROW) {
						controlmode = 1;
					} else if ((keyCode === 67 || keyCode === ENTER || keyCode === ESCAPE)) {
						controlmenu = false;
					}
				} else if (keyCode === 67) {
					controlmenu = true;
				} else if(currentState == 0) {
					if ((keyCode === UP_ARROW || keyCode === LEFT_ARROW) && currentChoice > 0) {
						currentChoice--;
						playSound(click);
					} else if ((keyCode === DOWN_ARROW || keyCode === RIGHT_ARROW) && currentChoice < 1) {
						currentChoice++;
						playSound(click);
					} /*else if (keyCode === ENTER && currentChoice == 1) {
						currentChoice = 0;
						currentState = 2;
						playSound(click);
					}*/ else if (keyCode === ENTER && currentChoice == 0) {
						currentState = 1;
						playSound(click);
						//background(bglevels);
					} else if ((keyCode === ENTER && currentChoice == 1 || keyCode === ESCAPE)) {
						playSound(click);
						if(confirm(\"Quit game?\")) {
							window.location.href = \"..\";
						}
					} 
				} else if(currentState == 1) {
					if (keyCode === LEFT_ARROW && currentChoice > 0) {
						currentChoice--;
						playSound(click);
					} else if (keyCode === RIGHT_ARROW && currentChoice < 20 /* 50 */ ) {
						currentChoice++;
						playSound(click);
					} else if (keyCode === UP_ARROW && currentChoice > 5) {
						currentChoice-=5;
						playSound(click);
					} else if (keyCode === DOWN_ARROW && currentChoice <= 15 /* 40 */ && currentChoice != 0) {
						currentChoice+=5;
						playSound(click);
					} else if (keyCode === UP_ARROW && currentChoice >= 1 && currentChoice <= 5) {
						currentChoice = 0;
						playSound(click);
					} else if (keyCode === DOWN_ARROW && currentChoice == 0) {
						currentChoice++;
						playSound(click);
					}
					if (keyCode === ENTER && currentChoice == 0 || keyCode === ESCAPE) {
						currentChoice = 0;
						currentState = 0;
						playSound(click);
					} else if (keyCode === ENTER && currentChoice != 0) {
						currentLevel = currentChoice;
						currentChoice = 0;
						currentState = 2;
						menumsc.stop();
						playSound(click);
					}
				} else if(currentState == 2) {
					if (keyCode === ESCAPE) {
						playerX = 250;
						playerY = 250;
						currentState = 1;
						playMusic(menumsc);
					}
				}
			}

			var mouseOnStart;
			var mouseOnQuit;

			function mouseMoved() {
				if(!controlmenu) {
					if(currentState == 0) {
						mouseOnStart = mouseX >= 602 && mouseY >= 285 && mouseX <= 865 && mouseY <= 350;
						mouseOnQuit = mouseX >= 602 && mouseY >= 352 && mouseX <= 865 && mouseY <= 418;
						if(mouseOnStart) {
							if(currentChoice != 0) {
								currentChoice = 0;
							}
						} else if(mouseOnQuit) {
							if(currentChoice != 1) {
								currentChoice = 1;
							}
						} else {
							currentChoice = -1;
						}
					}
				}
			}

			function mouseClicked() {
				if(!controlmenu) {
					if(currentState == 0) {
						if(mouseOnStart) {
							currentState = 1;
							playSound(click);
						} else if(mouseOnQuit) {
							playSound(click);
							if(confirm(\"Quit game?\")) {
								window.location.href = \"..\";
							}
						} else if(mouseX >= 605 && mouseY >= 424 && mouseX <= 674 && mouseY <= 493) {
							playMsc = !playMsc;
							if(playMsc) {
								playMusic(menumsc);
							} else {
								menumsc.stop();
							}
						} else if(mouseX >= 698 && mouseY >= 424 && mouseX <= 767 && mouseY <= 493) {
							playSnd = !playSnd;
						} else if(mouseX >= 791 && mouseY >= 424 && mouseX <= 860 && mouseY <= 493) {
							controlmenu = true;
						}
					}
				} else if(mouseX >= 791 && mouseY >= 424 && mouseX <= 860 && mouseY <= 493 && controlmenu) {
					controlmenu = false;
				} else if(mouseX >= 875 && mouseY >= 433 && mouseX <= 956 && mouseY <= 487 && controlmenu) {
					controlmode = 0;
				} else if(mouseX >= 975 && mouseY >= 433 && mouseX <= 1056 && mouseY <= 487 && controlmenu) {
					controlmode = 1;
				}
			}

			function playMusic(msc) {
				if(playMsc) {
					msc.loop();
				}
			}

			function playSound(snd) {
				if(playSnd) {
					snd.play();
				}
			}
		";
?>

<!DOCTYPE html>
<html>
	<head>
		<title>The Adventures Of Wietze</title>
		<style>
		body {
			background-image: url(./assets/site_background.jpeg);
			text-align: center;
		}
		#p5_loading {
			display: none;
		}
		canvas {
			border: 1px solid red;
			border-radius: 2px;
			cursor: url(./assets/cursor.cur), default;
		}
		</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="./p5/p5.min.js"></script>
		<script src="./p5/addons/p5.sound.min.js"></script>
		<script src="./p5play/lib/p5.play.js" type="text/javascript"></script>
		<script>
			<?php echo $script; ?>
		</script>
	</head>
	<body>
		<br>
		<img src="./assets/splash.png">
	</body>
</html>
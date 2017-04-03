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

function keyPressed() {
	if(currentState == 0) {
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
			if(confirm("Quit game?")) {
				window.location.href = "..";
			}
		}
	}
}
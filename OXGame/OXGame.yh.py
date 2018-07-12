#!/usr/bin/python
import os
def OXGame():
	BingoPlate = []
	for i in range(0, 9):
		BingoPlate.append(i+1)
	GameSet = False
	Bingo = ((0, 1, 2), (3, 4, 5), (6, 7, 8), (0, 3, 6), (1, 4, 7), (2, 5, 8), (0, 4, 8), (2, 4, 6))

	def BingoSet():
		os.system("clear")
		print("Using your number pad. (1P: X, 2P: O)\n")
		print "\t", BingoPlate[6], "|", BingoPlate[7], "|", BingoPlate[8] 
		print "\t---------"
		print "\t", BingoPlate[3], "|", BingoPlate[4], "|", BingoPlate[5] 
		print "\t---------"
		print "\t", BingoPlate[0], "|", BingoPlate[1], "|", BingoPlate[2], "\n" 

	def Player(P):
		P = int(P)
		n = Number()
		if type(BingoPlate[n]) != int:
			print("Position already used. Select others.\n")
			Player(P)
		else:
			if P == 0:
				BingoPlate[n] = "X"
			else:
				BingoPlate[n] = "O"
			
	def Number():
		while True:
			InputNumber = raw_input("Please select a position: ")
			try:
				InputNumber = int(InputNumber)
				InputNumber = InputNumber - 1
				if InputNumber in range(0, 9):
					return InputNumber
				else:
					print("Error input, please enter 1-9.")
					continue
			except ValueError:
				print("Please input a number.")
				continue

	def check_BingoPlate():
		count = 0
		for BPNumber in Bingo:
			if BingoPlate[BPNumber[0]] == BingoPlate[BPNumber[1]] == BingoPlate[BPNumber[2]]:
				if BingoPlate[BPNumber[0]] == "X":
					print("Player1 win.!\n")
				else:
					print("Player2 win.\n")
				return True
		for BPNumber in range(0,9):
			if type(BingoPlate[BPNumber]) != int:
				count += 1
			if count == 9:
				print("Tie\n")
				return True

	while not GameSet:
		for Round in range(0,9):
			BingoSet()
			GameSet = check_BingoPlate()
			if GameSet == True:
				break
			WhosTurn = Round % 2
			if WhosTurn == 0:
				print("Player1: X\n")
			else:
				print("Player2: O\n")
			Player(WhosTurn)

	if raw_input("Play again (y/n): ") == "y":
		OXGame()
	else:
		print("Game exit. See you.\n")

OXGame()

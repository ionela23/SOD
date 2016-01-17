
import os
import random
import socket
import time
import hashlib
import glob


path="//Ionela-pc/Users/Ionela/Desktop/SOD/done/work"

def create_list_files():
	l=[]
	os.chdir(path)
	l=glob.glob("**")
	random.shuffle(l)
	return l

def get_extension_file(filename):

	try:
		extension = filename.split(".")[-1]
	except ValueError:
		return ""
	return extension
	

def solve_file(done_me,file,ID):
	time.sleep(1)
	
	assigned_file=file + "." + ID + ".assign"
	print "File to solve: " + file + '\n'
	try:
		os.rename(file, assigned_file)
		GetFileMD5(assigned_file)
		os.rename(assigned_file, file + "." + ID + ".done")
	except WindowsError:
		print 'FILE NOT FOUND!'
	except IOError:
		print 'FILE DOES NOT OPEN!'	
	else: 
		done_me +=1
	return done_me
		
def get_ID():
	return socket.gethostbyname(socket.gethostname()).split('.')[3]

def GetFileMD5(filePath): 
	m = hashlib.md5()
	m.update(open(filePath,"rb").read())
	return m.hexdigest()

def get_all_done_by_me(ID):
	l=[]
	os.chdir(path)
	l=glob.glob("*."+ ID + ".done" )
	length = len(l)
	print "There are " + str(length) + " tasks done by " + str(ID) + ":\n"
	print l
	
done=1	
assigned=0
step=1
ID = get_ID()

while done>0 or assigned>0:
	if step==1:
		print "1.----------------------------------------"
		done=0
		assigned=0
		files=create_list_files()
		#print files

		for f in files:
			file_extension = get_extension_file(f)			
			 
			if file_extension!= "assigned" and file_extension!="done":
				done=solve_file(done,f,ID)
				#done +=1
				continue
			 
			elif file_extension=="assigned":
				assigned=assigned+1
				continue # go to next file
			 
			elif file_extension=="done":
				continue #go to next file
	else:
		print "2.-------------------------------------"
		done=0
		assigned=0
		
		files=create_list_files()
		#print files

		for f in files:
			file_extension = get_extension_file(f)
			#check if file is not assigned yet and not done
			if file_extension!= "assign" and file_extension!="done":
				done=done+1 
				break
			 
			elif file_extension=="assign":
				done=solve_file(done,f,ID)
				continue
		 
			elif file_extension=="done":
				continue
	
	
	if done>0:
		step=1
	elif assigned>0:
		step=2

get_all_done_by_me(ID)		
	
	
	
	
	
	
	
	
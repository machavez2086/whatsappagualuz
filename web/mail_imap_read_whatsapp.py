#!/usr/bin/env python
# -*- coding: utf-8 -*- 
#
# Very basic example of using Python 3 and IMAP to iterate over emails in a
# gmail folder/label.  This code is released into the public domain.
#
# This script is example code from this blog post:
# http://www.voidynullness.net/blog/2013/07/25/gmail-email-with-python-via-imap/
#
# This is an updated version of the original -- modified to work with Python 3.4.
#
import sys
import imaplib
import getpass
import email
import email.header
import datetime
import os
import re
import pymysql.cursors
import configparser
import zipfile
#import pytz
config = configparser.ConfigParser()
config.read("/var/www/html/sacspro/configuration.ini")
class WhatsappReadEmailExported(object):
    def __init__(self, connetc = True):
        self.EMAIL_ACCOUNT = config["email"]['email_account']
        self.EMAIL_PASSWOR = config["email"]['email_password']
        # Use 'INBOX' to read inbox.  Note that whatever folder is specified,
        # after successfully running this script all emails in that folder
        # will be marked as read.
        self.EMAIL_FOLDER = "Inbox"	

        self.host_db = config["database"]['db_host']
        self.user_db = config["database"]['db_user']
        self.password_db = config["database"]['db_password']
        self.db_name = config["database"]['db_name']
        self.connection = pymysql.connect(host=self.host_db,
                                     user=self.user_db,
                                     password=self.password_db,
                                     db=self.db_name,
                                     charset='utf8mb4',
                                     cursorclass=pymysql.cursors.DictCursor)
        if connetc:
            self.connetc_gmail()


    def process_mailbox(self, M):


        detach_dir = "/home/sacspro/email"
        """
        Do something with emails messages in the folder.
        For the sake of this example, print some headers.
        """

        #rv, data = M.search(None, "ALL")
        rv, data = M.search(None, "(UNSEEN)")
        if rv != 'OK':
            print("No messages found!")
            return

        for num in data[0].split():
            rv, data = M.fetch(num, '(RFC822)')
            if rv != 'OK':
                print("ERROR getting message", num)
                return

            msg = email.message_from_bytes(data[0][1])
            #print(msg['Subject'])
            #if not msg['Subject']:
            #    continue
            #hdr = email.header.make_header(email.header.decode_header(msg['Subject']))
            hdr = email.header.make_header(email.header.decode_header(msg['Subject']))
            subject = str(hdr)
            #print(subject)
            # if not "chat de whatsapp con" in subject.lower():
            #     continue
            #if not "whatsapp" in subject.lower():
            #    continue
            #print('Message %s: %s' % (num, subject))
            #print('Raw Date:', msg['Date'])
            # Now convert to local date-time
            date_tuple = email.utils.parsedate_tz(msg['Date'])
            if date_tuple:
                local_date = datetime.datetime.fromtimestamp(
                    email.utils.mktime_tz(date_tuple))
                print ("Local Date:", \
                    local_date.strftime("%a, %d %b %Y %H:%M:%S"))
            for part in msg.walk():
                print("entro")
                if part.get_content_maintype() == 'multipart':
                    continue
                if part.get('Content-Disposition') is None:
                    continue
                print("entro")
                filename = part.get_filename()
                att_path = os.path.join(detach_dir, filename)
                if os.path.isfile(att_path) :
                    os.remove(att_path)
                if not os.path.isfile(att_path) :
                    fp = open(att_path, 'wb')
                    fp.write(part.get_payload(decode=True))
                    fp.close()
                lines = []
                self.read_exported_group(att_path)


            #for part in msg.walk():
                #if part.get_content_maintype() == 'multipart':
            #    print(part.get_content_maintype())

    def read_exported_group(self, att_path):

        # Verificar si es un .zip de iphone
        filename = os.path.basename(att_path)
        if filename.endswith(".zip"):
            att_path = self.read_zip_from_iphone(att_path)
        whatsappgroup_id = self.get_group_from_file_name(filename)
        if not whatsappgroup_id:
            print(
                "el fichero analizado no posee el nombre de algun grupo configurado.")
            return
        # filename = os.path.basename(att_path)
        # filename = filename.replace(".txt", "")
        # group_name = filename.replace("Chat de WhatsApp con ", "")
        # cursor = self.connection.cursor()
        # cursor.execute(
        #     "SELECT * FROM whatsappgroup where name=%s",
        #     (group_name))
        # group = cursor.fetchall()
        # if len(group) == 0:
        #     print("el fichero analizado no posee el nombre de algun grupo configurado. Formato 'Chat de WhatsApp con nombreGrupo.txt'")
        #     sys.exit(0)
        # group = group[0]
        # whatsappgroup_id = group["id"]
        # print(group)
        #sys.exit(0)
        with open(att_path, encoding='utf-8') as f:
            lines = f.read().split("\n")
            # lines = f.read()
            # print(lines)
        f.closed
        alls = []
        anterior = ""
        for line in lines:
            m = re.search('(([0-9]{2}\/[0-9]{2}\/[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}))',
                          line)

            if m:
                if anterior.strip() != "":
                    alls.append(anterior)
                anterior = line
            else:
                if anterior.strip() != "":
                    anterior = anterior+"\n"+line
                else:
                    anterior = line



        date_before = None
        can_same_date = 0
        sames_arra = []
        for line in alls:

            line = line.strip()
            if line != "":
                dtmessage, contact_name, message = self.parse_file_exported(line)
                #print(contact_name)
                cursor = self.connection.cursor()
                if not message:
                    continue
                message = message.strip()
                
                # cursor.execute(
                #     "SELECT * FROM message where whatsappgroup_id=%s and strmenssagetext=%s",
                #     (whatsappgroup_id, message))
                try:
                    cursor.execute("SELECT * FROM message where whatsappgroup_id=%s and strmenssagetext=%s and dtmmessage >= %s and dtmmessage < %s and supportmember_id is NULL and clientmember_id is NULL", (whatsappgroup_id, message, dtmessage - datetime.timedelta(minutes=5), dtmessage + datetime.timedelta(minutes=5)))
                    # cursor.execute("SELECT * FROM message where whatsappgroup_id=%s and strmenssagetext=%s and supportmember_id is NULL and clientmember_id is NULL", (whatsappgroup_id, message))
                    messages = cursor.fetchall()
                    if len(messages) > 0:

                        message_object = messages[0]
                        cursor.execute(
                            "SELECT * FROM supportmember where whatsappnick=%s or phonenumber=%s",
                            (contact_name, contact_name))
                        supportmembers = cursor.fetchall()
                        supportmember_id = None
                        if len(supportmembers) > 0:
                            supportmember_id = supportmembers[0]["id"]

                        cursor.execute(
                            "SELECT * FROM clientmember where whatsappnick=%s or phonenumber=%s",
                            (contact_name, contact_name))
                        clientmembers = cursor.fetchall()
                        clientmember_id = None
                        if len(clientmembers) > 0:
                            clientmember_id = clientmembers[0]["id"]
                        cursor.execute("""
                                     UPDATE message
                                     SET mappedauthornick=%s, supportmember_id=%s, clientmember_id=%s
                                     WHERE id=%s
                                  """, (contact_name, supportmember_id, clientmember_id, message_object["id"]))
                        self.connection.commit()

                    #print(sames_arra)
                    if dtmessage == date_before:
                        can_same_date = can_same_date + 1

                    else:
                        can_same_date = 0
                        sames_arra = []
                    sames_arra.append(message)
                    date_before = dtmessage
                except:
                    print("error")
                    print(message)



                        # line_split = line.split("-")
                        # dtmessage = line_split[0]
                        # line_split.remove(dtmessage);
                        # dtmessage = dtmessage.strip()
                        # line = "-".join(line_split)
                        # line_split = line.split(":")
                        # contact_name = line_split[0]
                        # line_split.remove(contact_name);
                        # contact_name = contact_name.strip()
                        # message = ":".join(line_split).strip()
                        # dtmessage = datetime.datetime.strptime(dtmessage, "%m/%d/%y %I:%M %p")

    def connetc_gmail(self):
        M = imaplib.IMAP4(config["email"]['email_server'])

        try:
            #rv, data = M.login(EMAIL_ACCOUNT, getpass.getpass())
            rv, data = M.login(self.EMAIL_ACCOUNT, self.EMAIL_PASSWOR)
        except imaplib.IMAP4.error:
            print(imaplib.IMAP4.error)
            print ("LOGIN FAILED!!! ")
            sys.exit(1)

        print(rv, data)

        rv, mailboxes = M.list()
        if rv == 'OK':
            print("Mailboxes:")
            print(mailboxes)

        rv, data = M.select(self.EMAIL_FOLDER)
        if rv == 'OK':
            print("Processing mailbox...\n")
            self.process_mailbox(M)
            M.close()
        else:
            print("ERROR: Unable to open mailbox ", rv)

        M.logout()

    def read_zip_from_iphone(self, att_path):
        directory_to_extract_to = "/var/www/html/sacspro/tmp"
        zip_ref = zipfile.ZipFile(att_path, 'r')
        zip_ref.extractall(directory_to_extract_to)
        zip_ref.close()
        return directory_to_extract_to + "/_chat.txt"

    def get_group_from_file_name(self, filename):
        # print(filename)
        cursor = self.connection.cursor()
        cursor.execute(
            "SELECT * FROM whatsappgroup")
        groups = cursor.fetchall()
        for group in groups:
            if group["name"] in filename:
                return group["id"]
        return None
    def parse_file_exported(self, line):
        m = re.search('(.+(a. m.|p. m.))\s\-\s(.+):\s(.+)', line)
        if m:
            if len(m.groups()) > 3:
                dtmessage = m.group(1)
                dtmessage = dtmessage.replace("p. m.", "PM")
                dtmessage = dtmessage.replace("a. m.", "AM")
                contact_name = m.group(3)
                message = m.group(4)
                dtmessage = datetime.datetime.strptime(dtmessage, "%d/%m/%y %I:%M %p")
                return dtmessage, contact_name, message
        else:
            m = re.search('(.+([0-9]{2}\/[0-9]{2}\/[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2})\])\s(.+):\s(.+(?:\s|.)*)', line)

            if m:

                if len(m.groups()) > 3:
                    dtmessage = m.group(2)
                    contact_name = m.group(3)
                    message = m.group(4)

                    dtmessage = datetime.datetime.strptime(dtmessage, "%d/%m/%y %H:%M:%S")
                    # print(contact_name)
                    return dtmessage, contact_name, message
            else:
                print(line)
        print("none")
        return None, None, None

#att_path = os.path.join("email", "Chat de WhatsApp con Hola .txt")
#read_exported_group(att_path)
# w = WhatsappReadEmailExported()
# w.read_exported_group("/media/veracrypt1/Mexico/0revisar datos/Chat de WhatsApp con Hola.txt")

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS DepartmentFAQ;
DROP TABLE IF EXISTS FAQ;
DROP TABLE IF EXISTS AgentDepartment;
DROP TABLE IF EXISTS Department;
DROP TABLE IF EXISTS Message;
DROP TABLE IF EXISTS TicketTag;
DROP TABLE IF EXISTS FieldChange;
DROP TABLE IF EXISTS TicketHistory;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Status;
DROP TABLE IF EXISTS User;

CREATE TABLE User(
   userId INTEGER PRIMARY KEY,
   name VARCHAR NOT NULL,
   username VARCHAR  NOT NULL,
   email VARCHAR NOT NULL,
   password VARCHAR NOT NULL,
   reputation INTEGER NOT NULL,
   type VARCHAR NOT NULL,
   CHECK (type = "client" OR type = "agent" OR type = "admin")
);

CREATE TABLE Status(
   status VARCHAR PRIMARY KEY
);

CREATE TABLE Ticket(
   id INTEGER PRIMARY KEY,
   title VARCHAR NOT NULL,
   text VARCHAR NOT NULL,
   createDate DATETIME NOT NULL,
   visibility VARCHAR NOT NULL,
   priority VARCHAR NOT NULL,
   status VARCHAR REFERENCES Status(status) NOT NULL,
   category VARCHAR REFERENCES Department(category) ON DELETE SET NULL,
   creator INTEGER REFERENCES User(userId),
   replier INTEGER REFERENCES User(userId),
   feedback INTEGER DEFAULT 1 
);

CREATE TABLE TicketHistory(
   id INTEGER PRIMARY KEY,
   ticketId INTEGER REFERENCES Ticket(id),
   user INTEGER REFERENCES User(userId),
   date DATETIME NOT NULL,
   changes VARCHAR NOT NULL,
   field INTEGER REFERENCES FieldChange(id)
);


CREATE TABLE FieldChange(
   id INTEGER PRIMARY KEY,
   old_field VARCHAR NOT NULL,
   new_field VARCHAR NOT NULL
);


CREATE TABLE TicketTag(
   ticket INTEGER REFERENCES Ticket(id),
   tag VARCHAR NOT NULL,
   PRIMARY KEY (ticket, tag)
);

CREATE TABLE Message(
   id INTEGER PRIMARY KEY,
   user INTEGER REFERENCES User(userId) NOT NULL,
   ticket INTEGER REFERENCES Ticket(id) NOT NULL,
   text VARCHAR NOT NULL,
   date DATETIME NOT NULL
);

CREATE TABLE Department(
   category VARCHAR PRIMARY KEY
);

CREATE TABLE AgentDepartment(
   agent INTEGER REFERENCES User(userId),
   department VARCHAR REFERENCES Department(category) ON DELETE CASCADE, 
   PRIMARY KEY (agent, department)
);

CREATE TABLE FAQ(
   id INTEGER PRIMARY KEY,
   title VARCHAR NOT NULL,
   content VARCHAR NOT NULL
);

CREATE TABLE DepartmentFAQ(
   item INTEGER REFERENCES FAQ(id),
   category VARCHAR REFERENCES Department(category) ON DELETE SET NULL,
   PRIMARY KEY (item, category)
);


CREATE TRIGGER delete_tickets_on_department_delete
AFTER DELETE ON Department
FOR EACH ROW
BEGIN
   UPDATE Ticket SET category = 'none' WHERE category = OLD.category;
   UPDATE DepartmentFAQ SET category = 'none' WHERE category = OLD.category;
   DELETE FROM AgentDepartment WHERE department = OLD.category;
END;
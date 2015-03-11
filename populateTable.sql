insert into persons(111111, 'Poppy', 'Pomfrey', 'Hogwarts Infirmary', 'pomfrey@hogwarts.uk', '111-1111');
insert into persons(777777, 'Harry', 'Potter', '4 Privet Drive', 'potter@hogwarts.uk', '777-7777');
insert into persons(666666, 'Ron', 'Weasley', 'The Burrow','weasley@hogwarts.uk','666-6666');
insert into persons(555555, 'Hermione', 'Granger','London', 'granger@hogwarts.uk', '555-5555');  
insert into persons(222222, 'Hippocrates', 'Smethwyck', 'St Mungos Hospital for Magical Maladies and Injuries', 'smethwyck@mungos.uk', '222-2222');
insert into users(333333, 'Gilderoy', 'Lockhart', 'Hogwarts School of Witchcraft and Wizardry', 'lockhart@hogwarts.uk', '333-3333');
insert into users(444444, 'Abbott', 'Hannah', 'Hogwarts Infirmary', 'abbott@hogwarts.uk', '444-4444');
insert into users(888888, 'Longbottom', 'Neville', 'Hogwarts', 'longbottom@hogwarts.uk', '888-8888');
insert into users(999999, 'Lupin', 'Teddy', 'Hogwarts', 'lupin@hogwarts.uk', '999-9999');


insert into users('pomfrey', 'anapneo', 'doctor', 111111, to_date('1951-09-01', 'YYYY-MM-DD'); 
insert into users('potter', 'accio', 'patient', 777777,to_date('1980-31-07', 'YYYY-MM-DD'));
insert into users('granger','alohamora', 'patient', 666666, to_date('1979-09-19', 'YYYY-MM-DD');
insert into users('weasley','wingardiumLeviosa','patient', 555555,to_date('1980-03-01', 'YYYY-MM-DD'));
insert into users('lockhart', 'brackiumEmendo', 'radiologist', 333333, to_date('1992-09-01', 'YYYY-MM-DD'));
insert into users('smethwyck', 'rennervate', 'radiologist', 222222, to_date('1967-06-09', 'YYYY-MM-DD'));
insert into users('abbott', 'expectoPatronum', 'doctor', 444444, to_date('1980-10-12', 'YYYY-MM-DD'));
insert into users('longbottom', 'riddikulus', 'patient', 888888, to_date('1980-30-07', 'YYYY-MM-DD'));
insert into users('lupin', 'reducto', 'patient', 999999, to_date('1951-05-10', 'YYYY-MM-DD'));

insert into family_doctor(111111, 555555);
insert into family_doctor(111111, 666666);
insert into family_doctor(111111, 777777);
insert into family_doctor(444444, 888888);
insert into family_doctor(444444, 999999); 

insert into radiology_record (1, 777777, 111111, 222222, 'intuition', to_date('1982-09-27', 'YYYY-MM-DD'), 'broken arm bone', 'remove all the bones in that arm');
insert into radiology_record(2, 999999, 444444, 333333, 'science', to_date('2002-10-31', 'YYYY-MM-DD'), 'November 13 2002', 'broken leg', 'splint the bone');

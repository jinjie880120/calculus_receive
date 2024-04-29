import random 
import csv

def random_choose_student(student_array):
    num = random.randint(0, len(student_array))
    right_num = num - 1
    choose_student = student_array[right_num]
    del student_array[right_num]
    return student_array, choose_student

def choose_student_array(student_all_array, number):
    for_return_array = []
    for i in range(number):
        student_all_array, choose_student = random_choose_student(student_all_array)
        for_return_array.append(choose_student)
    return student_all_array, for_return_array

def choose_space_array(number):
    for_return_array = []
    space_str = ""
    for i in range(number):
        for_return_array.append(space_str)
    return for_return_array

def every_row_array(student_all_array, student_number, space_number):
    student_all_array, this_row_student = choose_student_array(student_all_array, student_number)
    this_row_space = choose_space_array(space_number)
    this_row_array = this_row_student + this_row_space
    return student_all_array, this_row_array

csvfile = open("Calculus_list.csv", "r", encoding="utf-8")
rows = csv.reader(csvfile)
student_all=[]
for row in rows:
    student_id = row[0].strip()
    student_id = student_id.replace("\ufeff","")
    name = row[1].strip()
    student = student_id + " " + name 
    student_all.append(student)

csvfile.close()
student_seat = student_all.copy()
writefile = open("Calculus_random.csv","w", encoding="utf-8-sig")
writer = csv.writer(writefile)
for i in range(13):
    if i == 0:
        student_seat, for_write_row = every_row_array(student_seat, 3, 5)
        writer.writerow(for_write_row)
    elif i >= 1 and i <= 4:
        student_seat, for_write_row = every_row_array(student_seat, 8, 0)
        writer.writerow(for_write_row)
    elif i == 5:
        student_seat, for_write_row = every_row_array(student_seat, 7, 1)
        writer.writerow(for_write_row)
    elif i >= 6 and i <= 11:
        student_seat, for_write_row = every_row_array(student_seat, 6, 2)
        writer.writerow(for_write_row)
    elif i == 12:
        for_write_row_all = choose_space_array(2)
        student_seat, for_write_row = every_row_array(student_seat, 4, 2)
        for_write_row_all += for_write_row
        writer.writerow(for_write_row_all)

writefile.close()



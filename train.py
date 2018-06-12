#!/usr/bin/python
# -*-coding:UTF-8-*-
import sys, signal, os, subprocess, time,string,MySQLdb,syslog,threading
reload(sys)
sys.setdefaultencoding('utf-8')
class onecode:
    def __init__(self,host='',user='',passwd='',db='',port=3306,charset='utf8',user_problem='',judge_detail='',problem=''):
        self.host = host
        self.user = user
        self.passwd = passwd
        self.db = db
        self.port = port
        self.charset = charset
        self.user_problem=user_problem
        self.judge_detail=judge_detail
        self.problem=problem
        self.conn = None
        self._conn()

    def _conn(self):
        try:
            self.conn = MySQLdb.Connection(self.host, self.user, self.passwd, self.db,charset=self.charset)
            return True
        except:
            return False

    def dbclose(self):
        self.conn.close()

    def _reConn(self, num=28800, stime=3):
        _number = 0
        _status = True
        while _status and _number <= num:

            try:
                self.conn.ping()
                _status = False
            except:
                if self._conn() == True:
                    _status = False
                    break
                _number += 1
                time.sleep(stime)

    def select(self, sql=''):
        try:
            self._reConn()
            self.cursor = self.conn.cursor(MySQLdb.cursors.DictCursor)
            self.cursor.execute(sql)
            result = self.cursor.fetchall()
            self.cursor.close()
            return result
        except MySQLdb.Error, e:
            # print "Error %d: %s" % (e.args[0], e.args[1])
            return False

    def select_limit(self, sql='', offset=0, length=20):
        sql = '%s limit %d , %d ;' % (sql, offset, length)
        return self.select(sql)

    def update(self, sql=''):
        try:
            self._reConn()
            self.cursor = self.conn.cursor(MySQLdb.cursors.DictCursor)
            result = self.cursor.execute(sql)
            self.conn.commit()
            self.cursor.close()
            return (True, result)
        except MySQLdb.Error, e:
            return False

    def close(self):
        self.conn.close()

    def modifyJudgeStatus(self,judge_id,time,memory,status):
        sql = "UPDATE %s SET judge_status = '%d',exe_time='%d',exe_memory='%d' WHERE id = '%d'" % (self.user_problem,status, time, memory, judge_id)
        self.update(sql)

    def modifyJudgeDetailStatus(self,judge_detail_id,time,memory,status):
        sql = "UPDATE %s SET judge_status = '%d',exe_time='%d',exe_memory='%d' WHERE id = '%d'" % (self.judge_detail,status, time, memory, judge_detail_id)
        self.update(sql)

    def modifyJudgeDetailAllStatus(self,row,status):
        records = self.queryJudgeDetailRecord(row['id'])
        for each in records:
            self.modifyJudgeDetailStatus(each['id'], 0, 0, status)  # 7 is Input Limit Exceeded
        self.modifyJudgeStatus(row['id'],0,0,status)  # 7 is Compiling

    # def modifyUserMessage(self,row,status):
    #     sql = "select * from user where id = '%d' " % (row['user_id'])
    #     user_data=self.select(sql)[0]
    #     acceptedNumber=int(user_data['accepted'])
    #     allsubmissionsNumber=int(user_data['submissions'])+1
    #     solveNumber=int(user_data['solve_problem'])
    #     submissionsProblemNumber=int(user_data['Submitted_problem'])
    #     if status == 0:
    #         acceptedNumber=acceptedNumber+1
    #         sql = "select  * from %s where judge_status = '%d' and user_id = '%d' and  problem_id = '%d' " % (self.user_problem, 0, row['user_id'],row['problem_id'])
    #         if len(self.select(sql)) == 1:
    #             solveNumber=solveNumber+1
    #     sql = "select * from %s where user_id = '%d' and problem_id = '%d' " % (self.user_problem,row['user_id'],row['problem_id'])
    #     if len(self.select(sql)) == 1:
    #         submissionsProblemNumber = submissionsProblemNumber + 1
    #     sql = "UPDATE user SET accepted = '%d',submissions='%d',solve_problem='%d',Submitted_problem='%d' WHERE id = '%d'" % (acceptedNumber, allsubmissionsNumber, solveNumber, submissionsProblemNumber, row['user_id'])
    #     self.update(sql)


    def modifyUserMessage(self,row,status):
        sql = "select  distinct problem_id from %s where judge_status = '%d' and user_id = '%d' " % (self.user_problem, 0, row['user_id'])
        problems=self.select(sql)
        solve_problem=len(problems)
        sql = "select  * from %s where  user_id = '%d' " % (self.user_problem, row['user_id'])
        allsubmissionsNumber=len(self.select(sql))
        library_score=0
        for each in problems:
            sql = "select  * from %s where  id = '%d' " % (self.problem, each['problem_id'])
            problem_difficulty=self.select(sql)[0]['difficulty']
            library_score=library_score+2*int(problem_difficulty)+5
        sql = "UPDATE user SET solve_problem = '%d',submissions='%d',library_score='%d' WHERE id = '%d'" % (solve_problem, allsubmissionsNumber, library_score, row['user_id'])
        self.update(sql)

    # def modifyProblemMessage(self,row,status):
    #     acceptedNumber=row['accepted']
    #     allsubmissionsNumber=row['submissions']+1
    #     if status == 0:
    #         acceptedNumber=acceptedNumber+1
    #     sql = "UPDATE %s SET accepted = '%d',submissions='%d' WHERE id = '%d'" % (self.problem,acceptedNumber, allsubmissionsNumber, row['id'])
    #     self.update(sql)

    def modifyProblemMessage(self,row,status):
        sql = "select  * from %s where judge_status = '%d' and problem_id = '%d' " % (self.user_problem, 0,row['id'])
        acceptedNumber=len(self.select(sql))
        sql = "select  * from %s where problem_id = '%d' " % (self.user_problem, row['id'])
        allsubmissionsNumber=len(self.select(sql))
        sql = "UPDATE %s SET accepted = '%d',submissions='%d' WHERE id = '%d'" % (self.problem,acceptedNumber, allsubmissionsNumber, row['id'])
        self.update(sql)

    def modifyContestJudgeMessage(self,judge_record,problem,status):
        sql = "select * from contest_judge where user_id = '%d' and  contest_id = '%d' and problem_id = '%d' " % (judge_record['user_id'],judge_record['user_id'],problem['id'])
        res=self.select(sql)
        if len(res)==1 :
            if status ==0 :
                sql = "UPDATE contest_judge SET status = '%d' WHERE id = '%d'" % (status,res[0]['id'])
                self.update(sql)
        else :
            sql = "INSERT INTO contest_judge(contest_id,user_id, problem_id, judge_status, score) VALUES ('%d','%d','%d','%d','%d')" % (judge_record['contest_id'],judge_record['user_id'],problem['id'],status,10)
            self.update(sql)

    def queryJudgeDetailRecord(self,judge_id):
        sql = "select * from %s where user_problem_id = '%d' " % (self.judge_detail,judge_id)
        return self.select(sql)

    def queryProblemRecord(self,problem_id):
        sql = "select * from %s where id = '%d' " % (self.problem,problem_id)
        return self.select(sql)

    def InputLimitExceeded(self,record):
        self.source_code_file_path = unicode(record['filepath'])
        self.source_code_file_path = self.source_code_file_path.encode('utf-8')
        a = open(self.source_code_file_path, "r")
        source_code = a.read()
        a.close()
        if len(source_code) >= 500000:
            return True
        return False

    def Compiling(self,record):
        self.father_file_path = "/".join(self.source_code_file_path.split("/")[:-1])
        ext = self.source_code_file_path.split(".")[1]
        compile = []
        if ext == "cpp":
            compile = "g++ -lm %s -o %s 2> /dev/null" % (self.source_code_file_path, self.father_file_path + "/a.out")
        elif ext == "c":
            compile = "gcc -lm %s -o %s 2> /dev/null" % (self.source_code_file_path, self.father_file_path + "/a.out")
        elif ext == "pas":
            compile = "fpc %s" % (self.source_code_file_path)
        if os.system(compile):
            return True
        if ext == "pas":
            runname = (self.source_code_file_path.split("/")[-1]).split(".")[0]
            self.run_file_path = "./" + runname
        else:
            self.run_file_path = "./a.out"
        return False

    def fileSame(self,a,b):
        fileA = open(a, 'r')
        fileB = open(b, 'r')
        fa = fileA.read()
        fb = fileB.read()
        fileA.close()
        fileB.close()
        fa = fa.replace('\r', '')
        fb = fb.replace('\r', '')
        fa = fa.rstrip('\t \n')
        fb = fb.rstrip('\t \n')
        if fa == fb:
            return True
        return False

    def fileAboutEqual(self,a,b):
        fileA = open(a, 'r')
        fileB = open(b, 'r')
        linesA = str(fileA.read())
        linesB = str(fileB.read())
        sa = linesA
        sb = linesB
        sa = sa.replace('\n','').replace(' ','').replace('\t','').replace('\r','')
        sb = sb.replace('\n', '').replace(' ', '').replace('\t', '').replace('\r','')
        fileA.close()
        fileB.close()
        if sa == sb:
            return True
        return False

    def judgeEveryCase(self,row,problemData):
        self.modifyJudgeDetailStatus(row['id'],0,0,10) # 10 is Running
        user_output_file_path = self.father_file_path + "/op"
        standard_output_file_path=row['output_file_path']
        p = subprocess.Popen(self.run_file_path, stdin=open(row['input_file_path'], "r"), stdout=open(user_output_file_path, "w"), stderr=open("/dev/null", "w"),cwd=self.father_file_path)
        start_time = time.time()
        max_time = 0
        max_memory = 0
        while p.poll() == None:
            s = open("/proc/" + str(p.pid) + "/status").read()
            if s.find('RSS') < 0:
                continue
            s = s[s.find('RSS') + 6:]
            s = s[:s.find('kB') - 1]
            if max_memory < int(s):
                max_memory = int(s)
            if max_memory >= int(problemData['memory_limit']):
                p.kill()
                return (max_time,max_memory,3)
            max_time = int((time.time() - start_time) * 1000)
            if max_time >= int(problemData['time_limit']):
                p.kill()
                return (max_time,max_memory,2)
        r = p.returncode
        if r != 0:
            return (max_time, max_memory, 4)
        a = open(user_output_file_path, 'r')
        codecode = a.read()
        if len(codecode) >= 20971520:
            return (max_time, max_memory, 6)
        if self.fileSame(user_output_file_path, standard_output_file_path) == True:
            return (max_time, max_memory, 0)
        else:
            if self.fileAboutEqual(user_output_file_path, standard_output_file_path) == True:
                return (max_time, max_memory, 11)
            else:
                return (max_time, max_memory, 1)
    def getMax(self,a,b):
        if a>b:
            return a
        return b
    def Query(self):
        sql = "select * from %s where judge_status = '%d' " % (self.user_problem,8)
        results=self.select(sql)
        for row in results:
            problemData = self.queryProblemRecord(row['problem_id'])
            if self.InputLimitExceeded(row) == True:
                self.modifyJudgeDetailAllStatus(row,7) #7 is Input Limit Exceeded
                continue
            self.modifyJudgeStatus(row['id'],0,0,9) #9 is Compiling
            if self.Compiling(row) == True:
                self.modifyJudgeDetailAllStatus(row, 5) #5 is Compilation Error
                continue
            self.modifyJudgeStatus(row['id'], 0, 0, 10)  # 10 is Running
            records = self.queryJudgeDetailRecord(row['id'])
            max_time=0
            max_memory=0
            status=-1
            for each in records:
                tmp_time,tmp_memory,tmp_status=self.judgeEveryCase(each,problemData[0])
                max_time=self.getMax(max_time,tmp_time)
                max_memory=self.getMax(max_memory,tmp_memory)
                status=self.getMax(status,tmp_status)
                self.modifyJudgeDetailStatus(each['id'], tmp_time, tmp_memory, tmp_status)
            self.modifyJudgeStatus(row['id'],max_time,max_memory,status)  # 9 is Compiling

    def polling(self):
        while True:
            self.Query()
            self.dbclose()
            time.sleep(1)


class myThread(threading.Thread):
    def __init__(self,user_problem,judge_detail,problem):
        threading.Thread.__init__(self)
        self.user_problem=user_problem
        self.judge_detail=judge_detail
        self.problem=problem

    def run(self):
        judge = onecode('localhost', 'root', 'OneCode', 'onecode', 3306, 'utf8', self.user_problem, self.judge_detail,self.problem)
        judge.polling()

if __name__ == '__main__':
    library = myThread('train_user_problem','train_judge_detail','train_problem')
    library.start()

    # library = onecode('localhost', 'root', 'OneCode', 'tcoj', 3306,'utf8','user_problem','judge_detail','problem')
    # library.polling()
    # contest = onecode('localhost', 'root', 'OneCode', 'tcoj', 3306, 'utf8', 'contest_user_problem', 'contest_judge_detail', 'contest_problem')
    # contest.polling()
    # print my.select_limit('select * from sdb_admin_roles', 1, 1)
    # my.close()

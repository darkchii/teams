<?php
    class TeamMember {
        public $team_id;
        public $user_id;
        public $is_leader;
        public $username;

        public function getTeamId() {
            return $this->team_id;
        }

        public function getUserId() {
            return $this->user_id;
        }

        public function getIsLeader() {
            return $this->is_leader;
        }

        public function getUsername() {
            return $this->username;
        }

        public static function getMembers($team_id) {
            $conn = DB::getConnection();
            $sql = "SELECT * FROM osu_teams_members ";
            //only join the first match in osu_users (theres usually 4 entries per user for mode statistics, but we only need the username)
            $sql .= "LEFT JOIN osu_users ON osu_teams_members.user_id = osu_users.id ";
            $sql .= "WHERE team_id = " . $team_id . " ";
            $sql .= "GROUP BY osu_teams_members.user_id ";
            var_dump($sql);
            //group by mode since we dont care
            $result = $conn->query($sql);
            $members = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $member = new TeamMember();
                    $member->team_id = $row['team_id'];
                    $member->user_id = $row['user_id'];
                    $member->is_leader = $row['is_leader'];
                    $member->username = $row['username'];
                    $members[] = $member;
                }
            }
            return $members;
        }
    }
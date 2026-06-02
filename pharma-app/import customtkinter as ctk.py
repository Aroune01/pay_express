import customtkinter as ctk
import random
from tkinter import messagebox, Canvas

# --- CONFIGURATION GLOBALE ---
ctk.set_appearance_mode("System")
ctk.set_default_color_theme("blue")

# --- PARAMÈTRES DU JEU ---
# Index de départ sur le plateau (cases 1 à 52) pour chaque couleur
START_POSITIONS = {
    "Rouge": 1,
    "Vert": 14,
    "Jaune": 27,
    "Bleu": 40
}
PLAYERS_COLORS = {
    "Rouge": "red",
    "Vert": "green",
    "Jaune": "gold",
    "Bleu": "blue"
}
# La position '0' signifie dans la base. 
# Les positions '53' à '58' signifient dans la colonne d'arrivée.
BOARD_SIZE = 52 # Nombre de cases principales

# --- CLASSE DE JEU LUDO ---
class LudoGame(ctk.CTk):
    def _init_(self):
        super()._init_()
        
        self.title("Jeu de Ludo Avancé 🎲")
        self.geometry("900x700")
        
        # --- État du Jeu ---
        self.players = list(PLAYERS_COLORS.keys())
        self.current_player_index = 0
        self.current_player = self.players[self.current_player_index]
        self.dice_value = 0
        self.pawn_positions = self._initialize_pawns() # { "Rouge": [0, 0, 0, 0], ... }
        self.pawn_objects = {} # Pour stocker les identifiants graphiques des pions sur le Canvas
        self.is_pawn_selection_mode = False
        
        # --- Interface ---
        self.grid_rowconfigure(0, weight=1)
        self.grid_columnconfigure(0, weight=3) # Plateau de jeu
        self.grid_columnconfigure(1, weight=1) # Panneau de contrôle

        self.board_frame = ctk.CTkFrame(self)
        self.board_frame.grid(row=0, column=0, padx=10, pady=10, sticky="nsew")
        
        self.control_panel = ctk.CTkFrame(self)
        self.control_panel.grid(row=0, column=1, padx=10, pady=10, sticky="nsew")
        
        self._setup_control_panel()
        self._setup_board_canvas()
        self._update_ui_state()

    def _initialize_pawns(self):
        """ Initialise tous les 16 pions dans leur base (position 0). """
        return {player: [0] * 4 for player in self.players}

    def _setup_control_panel(self):
        """ Crée le panneau de contrôle. """
        self.control_panel.grid_rowconfigure((0, 1, 2, 3, 4, 5, 6), weight=1)
        
        ctk.CTkLabel(self.control_panel, text="LUDO", font=ctk.CTkFont(size=24, weight="bold")).grid(row=0, column=0, pady=10, sticky="s")
        
        self.player_label = ctk.CTkLabel(self.control_panel, text="Joueur:", font=ctk.CTkFont(size=18))
        self.player_label.grid(row=1, column=0, pady=5)
        
        self.dice_label = ctk.CTkLabel(self.control_panel, text="Dé: ?", font=ctk.CTkFont(size=40, weight="bold"))
        self.dice_label.grid(row=2, column=0, pady=20)
        
        self.roll_button = ctk.CTkButton(self.control_panel, text="Lancer le Dé 🎲", command=self.roll_dice, height=50)
        self.roll_button.grid(row=3, column=0, pady=20, padx=20)
        
        self.status_label = ctk.CTkLabel(self.control_panel, text="Lancez le dé pour commencer.", wraplength=180)
        self.status_label.grid(row=4, column=0, pady=10)

    def _setup_board_canvas(self):
        """ Configure le Canvas et dessine le plateau initial. """
        self.board_canvas = Canvas(self.board_frame, bg="#EDEDED", highlightthickness=0)
        self.board_canvas.pack(fill="both", expand=True)
        self.board_canvas.bind("<Configure>", self._draw_board)

    def _draw_board(self, event=None):
        """ Dessine le plateau et les pions (simplifié pour la démo). """
        self.board_canvas.delete("all")
        
        w = self.board_canvas.winfo_width()
        h = self.board_canvas.winfo_height()
        size = min(w, h) * 0.9 # Taille du plateau
        x_offset = (w - size) / 2
        y_offset = (h - size) / 2

        # Dessiner les bases
        base_size = size / 2.2
        
        # Coordonnées des bases (très simplifiées)
        bases_coords = {
            "Rouge": (x_offset, y_offset, x_offset + base_size, y_offset + base_size),
            "Vert": (x_offset + size - base_size, y_offset, x_offset + size, y_offset + base_size),
            "Jaune": (x_offset + size - base_size, y_offset + size - base_size, x_offset + size, y_offset + size),
            "Bleu": (x_offset, y_offset + size - base_size, x_offset + base_size, y_offset + size)
        }
        
        for player, (x1, y1, x2, y2) in bases_coords.items():
            self.board_canvas.create_rectangle(x1, y1, x2, y2, fill=PLAYERS_COLORS[player], outline="black", width=2, tags="base")

        # Zone centrale d'arrivée
        center_x, center_y = w / 2, h / 2
        self.board_canvas.create_polygon(
            center_x, y_offset + base_size * 0.5, # Top
            center_x + base_size * 0.5, center_y, # Right
            center_x, y_offset + size - base_size * 0.5, # Bottom
            center_x - base_size * 0.5, center_y, # Left
            fill="#CCCCCC", outline="black", tags="center"
        )
        self.board_canvas.create_text(center_x, center_y, text="HOME", font=("Arial", 14, "bold"))

        self._draw_pawns(bases_coords)

    def _draw_pawns(self, bases_coords):
        """ Dessine les pions en fonction de leur position (Base ou Plateau). """
        pawn_radius = 10
        self.pawn_objects = {} # Réinitialiser les objets graphiques

        for player in self.players:
            self.pawn_objects[player] = []
            
            for i, pos in enumerate(self.pawn_positions[player]):
                x, y = 0, 0
                
                if pos == 0:
                    # Position dans la base (très simplifiée)
                    x1, y1, x2, y2 = bases_coords[player]
                    # Placement dans la base : 4 coins
                    if i == 0: x, y = x1 + 20, y1 + 20
                    elif i == 1: x, y = x2 - 20, y1 + 20
                    elif i == 2: x, y = x1 + 20, y2 - 20
                    elif i == 3: x, y = x2 - 20, y2 - 20
                    
                else:
                    # Placeholder: Position sur le plateau (cas 1-52 ou zone d'arrivée)
                    # Dans une vraie implémentation, on mapperait 'pos' (ex: 1, 14, 55) à des coordonnées X, Y précises.
                    # Ici, on place un pion sur une position fixe pour la démo s'il est sorti
                    if player == "Rouge" and pos >= 1:
                         w = self.board_canvas.winfo_width()
                         h = self.board_canvas.winfo_height()
                         x, y = w / 2 - 100 + i*10, h/2 - 150 # Position temporaire de démo
                    else:
                        continue # Ne pas dessiner les autres pions tant qu'ils sont à 0 dans la démo

                if x != 0 and y != 0:
                    # Dessiner le pion
                    pawn_id = self.board_canvas.create_oval(
                        x - pawn_radius, y - pawn_radius,
                        x + pawn_radius, y + pawn_radius,
                        fill=PLAYERS_COLORS[player], outline="black", width=1,
                        tags=(f"pawn_{player}_{i}", "pawn")
                    )
                    self.pawn_objects[player].append(pawn_id)
                    # Lier l'événement de clic pour la sélection
                    self.board_canvas.tag_bind(pawn_id, "<Button-1>", lambda e, p=player, idx=i: self._select_pawn(e, p, idx))


    # -----------------------------------------------------------------
    # --- LOGIQUE DE JEU ---
    # -----------------------------------------------------------------

    def roll_dice(self):
        """ Lance le dé et initialise la phase de sélection de pion. """
        
        if self.is_pawn_selection_mode:
            messagebox.showwarning("Action requise", "Veuillez d'abord sélectionner un pion pour le mouvement.")
            return

        self.dice_value = random.randint(1, 6)
        self.dice_label.configure(text=f"Dé: {self.dice_value}")
        
        if self._can_player_move():
            self.status_label.configure(text=f"{self.current_player} a obtenu un {self.dice_value}. Cliquez sur le pion à déplacer.", text_color=PLAYERS_COLORS[self.current_player])
            self.is_pawn_selection_mode = True
            self.roll_button.configure(state="disabled")
        else:
            self.status_label.configure(text=f"{self.current_player} ne peut pas jouer avec {self.dice_value}. Passage au joueur suivant.", text_color="grey")
            # Petit délai avant de passer au joueur suivant
            self.after(1000, self._end_turn)

    def _can_player_move(self):
        """ Vérifie si au moins un mouvement est possible avec le résultat du dé. """
        
        # 1. Si 6, on peut sortir un pion (si un est à la maison) ou avancer un pion sorti.
        if self.dice_value == 6:
            return True # Au moins la sortie est possible si un pion est à la maison, ou avancer si un est sorti.
        
        # 2. Si pas 6, on doit pouvoir avancer un pion qui est déjà sorti (position > 0)
        if any(pos > 0 for pos in self.pawn_positions[self.current_player]):
            # Logique complète : vérifier si (pos + dice_value) est un mouvement valide (pas d'overshoot de la zone d'arrivée)
            return True
        
        return False
        
    def _select_pawn(self, event, player, pawn_index):
        """ Gère le clic du joueur sur un pion. """
        if not self.is_pawn_selection_mode or player != self.current_player:
            # Ne peut sélectionner que si c'est le mode de sélection et si c'est le pion du joueur actuel
            return

        pawn_pos = self.pawn_positions[player][pawn_index]
        new_pos = pawn_pos + self.dice_value
        
        # --- Logique de mouvement ---
        is_valid_move = False
        
        # Cas 1: Sortir de la base
        if pawn_pos == 0 and self.dice_value == 6:
            new_pos = START_POSITIONS[player]
            is_valid_move = True
        
        # Cas 2: Avancer sur le plateau
        elif pawn_pos > 0:
            # Simplification : Mouvement valide si on ne dépasse pas la case finale
            # Logique complète ici : gestion des 52 cases, de la zone d'arrivée et des captures.
            if new_pos <= (BOARD_SIZE + 6): # 52 + 6 cases d'arrivée (très simplifié)
                is_valid_move = True
        
        if is_valid_move:
            self.pawn_positions[player][pawn_index] = new_pos
            self.status_label.configure(text=f"{player} a déplacé le pion {pawn_index+1} à la position {new_pos}.")
            self._end_turn(is_six_roll=(self.dice_value == 6))
            self._draw_board() # Redessiner pour mettre à jour la position
        else:
            self.status_label.configure(text="Mouvement invalide! Choisissez un autre pion.", text_color="red")


    def _end_turn(self, is_six_roll=False):
        """ Termine le tour en changeant de joueur ou en le laissant rejouer. """
        self.is_pawn_selection_mode = False
        self.roll_button.configure(state="normal")
        self.dice_value = 0
        self.dice_label.configure(text="Dé: ?")

        if not is_six_roll:
            self._check_for_win()
            self.switch_player()
        else:
            # Laisse le même joueur rejouer après un 6
            self.status_label.configure(text=f"{self.current_player} a obtenu un 6 et rejoue! Lancez le dé.")

    def _check_for_win(self):
        """ Vérifie si le joueur actuel a gagné. """
        # La condition de victoire est que les 4 pions soient dans la zone 'HOME' (pos 53 à 58)
        # Simplification pour la démo : 4 pions > 52 est la victoire.
        if all(pos > BOARD_SIZE for pos in self.pawn_positions[self.current_player]):
            messagebox.showinfo("VICTOIRE!", f"Félicitations, le joueur {self.current_player} a gagné la partie!")
            self.destroy() # Fermer l'application après la victoire

    def switch_player(self):
        """ Passe au joueur suivant. """
        self.current_player_index = (self.current_player_index + 1) % len(self.players)
        self.current_player = self.players[self.current_player_index]
        self._update_ui_state()

    def _update_ui_state(self):
        """ Met à jour l'affichage du joueur actuel. """
        color = PLAYERS_COLORS.get(self.current_player)
        self.player_label.configure(
            text=f"Joueur: {self.current_player}",
            text_color=color
        )
        self.status_label.configure(text=f"C'est le tour de {self.current_player}. Lancez le dé.", text_color="black")


# --- EXÉCUTION DE L'APPLICATION ---
if _name_ == "_main_":
    app = LudoGame()
    app.mainloop()
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { User } from '@prisma/client';
import { UsersService } from '@/users/users.service';
import { LoginUser } from '@/auth/types/login-user.type';
import { JwtPayload } from '@/auth/types/jwt-payload.type';
import { SpotsService } from '@/spots/spots.service';
import * as bcrypt from 'bcrypt';
import * as crypto from 'crypto';
import { Jwt } from '@/auth/types/jwt.type';

@Injectable()
export class AuthService {
	constructor(
		private usersService: UsersService,
		private jwtService: JwtService,
		private spotsService: SpotsService,
	) {}

	async validateCredentials({ username, password }: LoginUser): Promise<User> {
		const user = (await this.usersService.getUserByUsername(username)) as User;

		const passwordsMatch = bcrypt.compareSync(password, user.password);

		if (!user.enabled || !passwordsMatch) {
			throw new UnauthorizedException();
		}

		return user;
	}

	async generateJwt(user: User): Promise<Jwt> {
		const payload: JwtPayload = {
			sub: user.id,
			username: user.username,
			email: user.email,
			role: user.role,
		};

		return {
			jwt: await this.jwtService.signAsync(payload),
		};
	}

	async validateJwtPayload({ sub }: JwtPayload): Promise<User> {
		const user = (await this.usersService.getUser(sub)) as User;

		if (!user.enabled) {
			throw new UnauthorizedException();
		}

		return user;
	}

	async validateToken(value: string) {
		const hash = crypto.createHash('sha256').update(value).digest('hex');

		const user = (await this.usersService.getUserByTokenHash(hash)) as User;

		if (!user.enabled) {
			throw new UnauthorizedException();
		}

		return user;
	}

	async validateSpot(spotId: string) {
		const spot = await this.spotsService.getSpot(spotId);

		if (spot.configured) {
			throw new UnauthorizedException();
		}

		const user = (await this.usersService.getUser(spot.userId)) as User;

		return user;
	}
}

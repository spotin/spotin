import { Injectable, UnauthorizedException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { User } from '@prisma/client';
import { UsersService } from '@/users/users.service';
import { LoginUser } from '@/auth/local/types/login-user.type';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { SpotsService } from '@/spots/spots.service';
import * as argon2id from 'argon2';
import * as crypto from 'crypto';
import { Jwt } from '@/auth/jwt/types/jwt.type';

@Injectable()
export class AuthService {
	constructor(
		private readonly jwtService: JwtService,
		private readonly spotsService: SpotsService,
		private readonly usersService: UsersService,
	) {}

	async validateCredentials({ email, password }: LoginUser): Promise<User> {
		const user = (await this.usersService.getUserByEmail(email)) as User;

		const passwordsMatch = await argon2id.verify(
			user.password,
			password as string,
		);

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

	async validatePasswordResetRequestToken(token: string): Promise<User> {
		const user = (await this.usersService.getUserByResetPasswordRequestToken(
			token,
		)) as User;

		if (!user) {
			throw new UnauthorizedException();
		}

		return user;
	}
}
